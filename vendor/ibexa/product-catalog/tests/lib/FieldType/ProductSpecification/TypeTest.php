<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\FieldType\ProductSpecification;

use Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Core\FieldType\ValidationError;
use Ibexa\ProductCatalog\FieldType\ProductSpecification\Type;
use Ibexa\ProductCatalog\FieldType\ProductSpecification\Value;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\Product\HandlerInterface;
use Ibexa\Tests\Core\FieldType\BaseFieldTypeTest;

/**
 * @covers \Ibexa\ProductCatalog\FieldType\ProductSpecification\Type
 */
final class TypeTest extends BaseFieldTypeTest
{
    private const EXISTING_PRODUCT_CODE = 'EXISTING_CODE';
    private const DUPLICATED_PRODUCT_CODE = 'DUPLICATED_CODE';

    private HandlerInterface $productHandler;

    protected function setUp(): void
    {
        $this->productHandler = $this->createMock(HandlerInterface::class);
        $this->productHandler
            ->method('isCodeUnique')
            ->with(self::DUPLICATED_PRODUCT_CODE)
            ->willReturn(false);
    }

    protected function provideFieldTypeIdentifier(): string
    {
        return Type::FIELD_TYPE_IDENTIFIER;
    }

    protected function createFieldTypeUnderTest(): Type
    {
        return new Type($this->productHandler);
    }

    /**
     * @return iterable<mixed>
     */
    protected function getValidatorConfigurationSchemaExpectation(): iterable
    {
        return [];
    }

    /**
     * @return iterable<mixed>
     */
    protected function getSettingsSchemaExpectation(): iterable
    {
        return [
            'attributes_definitions' => [
                'type' => 'hash',
                'default' => [],
            ],
            'regions' => [
                'type' => 'hash',
                'default' => [],
            ],
            'is_virtual' => [
                'type' => 'boolean',
                'default' => false,
            ],
        ];
    }

    protected function getEmptyValueExpectation(): Value
    {
        return new Value();
    }

    /**
     * @return iterable<array{
     *     int|\Ibexa\Contracts\Core\Repository\Values\ValueObject,
     *     class-string<\Exception>,
     * }>
     */
    public function provideInvalidInputForAcceptValue(): iterable
    {
        yield [
            0xFFFF,
            InvalidArgumentException::class,
        ];

        yield [
            $this->createMock(ValueObject::class),
            InvalidArgumentException::class,
        ];
    }

    /**
     * @return iterable<string, array{
     *     null|array<mixed>|\Ibexa\ProductCatalog\FieldType\ProductSpecification\Value,
     *     \Ibexa\ProductCatalog\FieldType\ProductSpecification\Value,
     * }>
     */
    public function provideValidInputForAcceptValue(): iterable
    {
        yield 'null' => [
            null,
            new Value(),
        ];

        yield 'array' => [
            [
                'code' => self::EXISTING_PRODUCT_CODE,
            ],
            new Value(null, self::EXISTING_PRODUCT_CODE),
        ];

        yield 'value' => [
            new Value(null, self::EXISTING_PRODUCT_CODE),
            new Value(null, self::EXISTING_PRODUCT_CODE),
        ];
    }

    /**
     * @return iterable<array{
     *     \Ibexa\ProductCatalog\FieldType\ProductSpecification\Value,
     *     null|array<mixed>,
     * }>
     */
    public function provideInputForToHash(): iterable
    {
        yield [
            new Value(),
            null,
        ];

        yield [
            new Value(null, self::EXISTING_PRODUCT_CODE),
            [
                'id' => null,
                'code' => self::EXISTING_PRODUCT_CODE,
                'attributes' => [],
                'is_virtual' => false,
            ],
        ];
    }

    /**
     * @return iterable<array{
     *     null|string,
     *     \Ibexa\ProductCatalog\FieldType\ProductSpecification\Value,
     * }>
     */
    public function provideInputForFromHash(): iterable
    {
        yield [
            null,
            new Value(),
        ];

        yield [
            '',
            new Value(),
        ];
    }

    /**
     * @return array<array{
     *     \Ibexa\ProductCatalog\FieldType\ProductSpecification\Value,
     *     string,
     * }>
     */
    public function provideDataForGetName(): array
    {
        return [
            [new Value(), ''],
            [new Value(null, self::EXISTING_PRODUCT_CODE), self::EXISTING_PRODUCT_CODE],
        ];
    }

    /**
     * @return iterable<string, array{
     *     array<mixed>,
     *     \Ibexa\ProductCatalog\FieldType\ProductSpecification\Value,
     * }>
     */
    public function provideValidDataForValidate(): iterable
    {
        yield 'empty value' => [
            [],
            new Value(),
        ];

        yield 'product code "0"' => [
            [
                'code' => '0',
            ],
            new Value(null, '0'),
        ];
    }

    /**
     * @return iterable<string, array{
     *     array<mixed>,
     *     \Ibexa\ProductCatalog\FieldType\ProductSpecification\Value,
     *     array<\Ibexa\Core\FieldType\ValidationError>,
     * }>
     */
    public function provideInvalidDataForValidate(): iterable
    {
        yield 'missing code' => [
            [],
            new Value(null, ''),
            [
                new ValidationError(
                    'ibexa.product.code.required',
                    null,
                    [],
                    'code'
                ),
            ],
        ];

        $value = new Value(null);
        $value->setCode(self::DUPLICATED_PRODUCT_CODE);

        yield 'duplicated code' => [
            [],
            $value,
            [
                new ValidationError(
                    'ibexa.product.code.unique',
                    null,
                    [
                        ':code' => self::DUPLICATED_PRODUCT_CODE,
                    ],
                    'code'
                ),
            ],
        ];
    }

    /**
     * @return iterable<array<mixed>>
     */
    public function provideValidFieldSettings(): iterable
    {
        yield [
            [],
        ];

        yield [
            [
                'attributes_definitions' => [
                    [
                        'attributeDefinition' => 'foo',
                        'discriminator' => false,
                        'required' => true,
                    ],
                    [
                        'attributeDefinition' => 'bar',
                        'discriminator' => true,
                        'required' => true,
                    ],
                    [
                        'attributeDefinition' => 'baz',
                        'discriminator' => true,
                        'required' => false,
                    ],
                ],
                'is_virtual' => true,
            ],
        ];
    }
}
