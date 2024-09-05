<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\FieldType\CustomerGroup;

use Ibexa\Core\Base\Exceptions\InvalidArgumentException;
use Ibexa\Core\FieldType\ValidationError;
use Ibexa\ProductCatalog\FieldType\CustomerGroup\Type;
use Ibexa\ProductCatalog\FieldType\CustomerGroup\Value;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\CustomerGroup\HandlerInterface;
use Ibexa\ProductCatalog\Local\Repository\Values\CustomerGroup;
use Ibexa\ProductCatalog\Values\ProxyFactory\CustomerGroupProxyFactoryInterface;
use Ibexa\Tests\Core\FieldType\FieldTypeTest;

/**
 * @covers \Ibexa\ProductCatalog\FieldType\CustomerGroup\Type
 */
final class TypeTest extends FieldTypeTest
{
    private ?CustomerGroupProxyFactoryInterface $customerGroupProxyFactoryMock = null;

    private ?CustomerGroup $customerGroupMock = null;

    private function getCustomerGroupProxyFactoryMock(): CustomerGroupProxyFactoryInterface
    {
        if (null === $this->customerGroupProxyFactoryMock) {
            $this->customerGroupProxyFactoryMock = self::createMock(CustomerGroupProxyFactoryInterface::class);
            $this->customerGroupProxyFactoryMock
                ->method('createCustomerGroupProxy')
                ->willReturn($this->getCustomerGroupMock());
        }

        return $this->customerGroupProxyFactoryMock;
    }

    private function getCustomerGroupMock(): CustomerGroup
    {
        if (null === $this->customerGroupMock) {
            $this->customerGroupMock = self::createMock(CustomerGroup::class);
        }

        return $this->customerGroupMock;
    }

    protected function provideFieldTypeIdentifier(): string
    {
        return Type::FIELD_TYPE_IDENTIFIER;
    }

    protected function createFieldTypeUnderTest(): Type
    {
        $handler = $this->createMock(HandlerInterface::class);

        $handler->method('exists')
            ->willReturnCallback(static function (int $id): bool {
                switch ($id) {
                    case 1: return true;
                    case 2: return false;
                }

                return false;
            });

        return new Type(
            $handler,
            $this->getCustomerGroupProxyFactoryMock()
        );
    }

    /**
     * @return array{}
     */
    protected function getValidatorConfigurationSchemaExpectation(): array
    {
        return [];
    }

    /**
     * @return array{}
     */
    protected function getSettingsSchemaExpectation(): array
    {
        return [];
    }

    protected function getEmptyValueExpectation(): Value
    {
        return new Value(null);
    }

    /**
     * @return iterable<array{
     *     string,
     *     class-string<\Exception>,
     * }>
     */
    public function provideInvalidInputForAcceptValue(): iterable
    {
        yield [
            '',
            InvalidArgumentException::class,
        ];
    }

    /**
     * @return iterable<array{
     *     null|array|\Ibexa\ProductCatalog\FieldType\CustomerGroup\Value,
     *     \Ibexa\ProductCatalog\FieldType\CustomerGroup\Value,
     * }>
     */
    public function provideValidInputForAcceptValue(): iterable
    {
        yield [
            null,
            new Value(null),
        ];

        yield [
            [
                'customer_group_id' => 1,
            ],
            new Value(1, $this->getCustomerGroupMock()),
        ];

        yield [
            new Value(null),
            new Value(null),
        ];
    }

    /**
     * @return iterable<array{
     *     \Ibexa\ProductCatalog\FieldType\CustomerGroup\Value,
     *     null|array,
     * }>
     */
    public function provideInputForToHash(): iterable
    {
        yield [
            new Value(null),
            null,
        ];

        yield [
            new Value(1),
            [
                'customer_group_id' => 1,
            ],
        ];
    }

    /**
     * @return array<array{
     *     null|array|string,
     *     \Ibexa\ProductCatalog\FieldType\CustomerGroup\Value,
     * }>
     */
    public function provideInputForFromHash(): array
    {
        return [
            [
                null,
                new Value(null),
            ], [
                '',
                new Value(null),
            ],
        ];
    }

    /**
     * @return array<array{
     *     \Ibexa\ProductCatalog\FieldType\CustomerGroup\Value,
     *     string|int,
     *     2?: array,
     *     3?: string,
     * }>
     */
    public function provideDataForGetName(): array
    {
        return [
            [new Value(1), '1'],
            [new Value(null), ''],
        ];
    }

    /**
     * @return iterable<string, array{
     *     array,
     *     \Ibexa\ProductCatalog\FieldType\CustomerGroup\Value,
     * }>
     */
    public function provideValidDataForValidate(): iterable
    {
        yield 'empty value' => [
            [],
            new Value(null),
        ];
    }

    /**
     * @return iterable<string, array{
     *     array,
     *     \Ibexa\ProductCatalog\FieldType\CustomerGroup\Value,
     *     array<\Ibexa\Core\FieldType\ValidationError>,
     * }>
     */
    public function provideInvalidDataForValidate(): iterable
    {
        yield 'Missing Customer Group ID' => [
            [],
            new Value(2),
            [
                new ValidationError(
                    'Customer group with ID :id does not exist',
                    null,
                    [
                        ':id' => 2,
                    ],
                    'id'
                ),
            ],
        ];
    }
}
