<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\Form\DataTransformer;

use Exception;
use Ibexa\Bundle\ProductCatalog\Form\DataTransformer\ProductTypeTransformer;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException;
use Ibexa\Contracts\ProductCatalog\ProductTypeServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductTypeInterface;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\Form\Exception\TransformationFailedException;

final class ProductTypeTransformerTest extends TestCase
{
    private const PRODUCT_TYPE_IDENTIFIER = 'identifier';

    /** @var \Ibexa\Contracts\ProductCatalog\ProductTypeServiceInterface|\PHPUnit\Framework\MockObject\MockObject */
    private ProductTypeServiceInterface $productTypeService;

    private ProductTypeTransformer $transformer;

    protected function setUp(): void
    {
        $this->productTypeService = $this->createMock(ProductTypeServiceInterface::class);
        $this->transformer = new ProductTypeTransformer($this->productTypeService);
    }

    /**
     * @dataProvider dataProviderForTestTransform
     */
    public function testTransform(?ProductTypeInterface $value, ?string $expected): void
    {
        self::assertEquals($expected, $this->transformer->transform($value));
    }

    /**
     * @return iterable<string,mixed>
     */
    public function dataProviderForTestTransform(): iterable
    {
        $productType = $this->createMock(ProductTypeInterface::class);
        $productType->method('getIdentifier')->willReturn(self::PRODUCT_TYPE_IDENTIFIER);

        yield 'null' => [null, null];
        yield 'Product Type' => [$productType, self::PRODUCT_TYPE_IDENTIFIER];
    }

    public function testTransformWithInvalidInput(): void
    {
        $this->expectException(TransformationFailedException::class);
        $this->expectExceptionMessage('Expected a ' . ProductTypeInterface::class . ' object.');

        $this->transformer->transform(new stdClass());
    }

    public function testReverseTransform(): void
    {
        $productType = $this->createMock(ProductTypeInterface::class);

        $this->productTypeService
            ->method('getProductType')
            ->with(self::PRODUCT_TYPE_IDENTIFIER)
            ->willReturn($productType);

        self::assertEquals(
            $productType,
            $this->transformer->reverseTransform(self::PRODUCT_TYPE_IDENTIFIER)
        );
    }

    /**
     * @dataProvider dataProviderForTestReverseTransformWithInvalidInput
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
    public function dataProviderForTestReverseTransformWithInvalidInput(): iterable
    {
        yield 'integer' => [123456];
        yield 'bool' => [true];
        yield 'float' => [12.34];
        yield 'array' => [['element']];
        yield 'object' => [new stdClass()];
    }

    /**
     * @dataProvider dataProviderForTestReverseTransformHandleProductLoadFailure
     */
    public function testReverseTransformHandleProductLoadFailure(Exception $exception): void
    {
        $this->expectException(TransformationFailedException::class);

        $this->productTypeService
            ->method('getProductType')
            ->with(self::PRODUCT_TYPE_IDENTIFIER)
            ->willThrowException($exception);

        $this->transformer->reverseTransform(self::PRODUCT_TYPE_IDENTIFIER);
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
