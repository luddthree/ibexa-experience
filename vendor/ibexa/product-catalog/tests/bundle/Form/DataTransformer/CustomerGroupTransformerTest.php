<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\Form\DataTransformer;

use Exception;
use Ibexa\Bundle\ProductCatalog\Form\DataTransformer\CustomerGroupTransformer;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException;
use Ibexa\Contracts\ProductCatalog\CustomerGroupServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroupInterface;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * @covers \Ibexa\Bundle\ProductCatalog\Form\DataTransformer\CustomerGroupTransformer
 */
final class CustomerGroupTransformerTest extends TestCase
{
    private const EXAMPLE_CUSTOMER_GROUP_ID = 42;

    /** @var \Ibexa\Contracts\ProductCatalog\CustomerGroupServiceInterface|\PHPUnit\Framework\MockObject\MockObject */
    private CustomerGroupServiceInterface $customerGroupService;

    private CustomerGroupTransformer $transformer;

    protected function setUp(): void
    {
        $this->customerGroupService = $this->createMock(CustomerGroupServiceInterface::class);
        $this->transformer = new CustomerGroupTransformer($this->customerGroupService);
    }

    /**
     * @dataProvider dataProviderForTestTransformDataProvider
     */
    public function testTransform(?CustomerGroupInterface $value, ?int $expected): void
    {
        self::assertEquals($expected, $this->transformer->transform($value));
    }

    /**
     * @return iterable<string,mixed>
     */
    public function dataProviderForTestTransformDataProvider(): iterable
    {
        $customerGroup = $this->createMock(CustomerGroupInterface::class);
        $customerGroup->method('getId')->willReturn(self::EXAMPLE_CUSTOMER_GROUP_ID);

        yield 'null' => [null, null];
        yield 'CustomerGroup object' => [$customerGroup, self::EXAMPLE_CUSTOMER_GROUP_ID];
    }

    public function testTransformWithInvalidInput(): void
    {
        $this->expectException(TransformationFailedException::class);
        $this->expectExceptionMessage('Expected a ' . CustomerGroupInterface::class . ' object.');

        $this->transformer->transform(new stdClass());
    }

    public function testReverseTransform(): void
    {
        $customerGroup = $this->createMock(CustomerGroupInterface::class);

        $this->customerGroupService
            ->method('getCustomerGroup')
            ->with(self::EXAMPLE_CUSTOMER_GROUP_ID)
            ->willReturn($customerGroup);

        self::assertEquals($customerGroup, $this->transformer->reverseTransform(self::EXAMPLE_CUSTOMER_GROUP_ID));
    }

    /**
     * @dataProvider dataProviderForTestReverseTransformWithInvalidInputDataProvider
     *
     * @param mixed $value
     */
    public function testReverseTransformWithInvalidInput($value): void
    {
        $this->expectException(TransformationFailedException::class);

        $this->transformer->reverseTransform($value);
    }

    /**
     * @return iterable<string,array{mixed}>
     */
    public function dataProviderForTestReverseTransformWithInvalidInputDataProvider(): iterable
    {
        yield 'bool' => [true];
        yield 'non-numeric string' => ['foo'];
        yield 'array' => [['element']];
        yield 'object' => [new stdClass()];
    }

    /**
     * @dataProvider dataProviderForTestReverseTransformHandleProductLoadFailure
     */
    public function testReverseTransformHandleProductLoadFailure(Exception $exception): void
    {
        $this->expectException(TransformationFailedException::class);

        $this->customerGroupService
            ->method('getCustomerGroup')
            ->with(self::EXAMPLE_CUSTOMER_GROUP_ID)
            ->willThrowException($exception);

        $this->transformer->reverseTransform(self::EXAMPLE_CUSTOMER_GROUP_ID);
    }

    /**
     * @return iterable<string,mixed>
     */
    public function dataProviderForTestReverseTransformHandleProductLoadFailure(): iterable
    {
        yield NotFoundException::class => [
            $this->createMock(NotFoundException::class),
        ];

        yield UnauthorizedException::class => [
            $this->createMock(UnauthorizedException::class),
        ];
    }
}
