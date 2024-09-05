<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\Form\DataTransformer;

use Exception;
use Ibexa\Bundle\ProductCatalog\Form\DataTransformer\ProductTransformer;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException;
use Ibexa\Contracts\ProductCatalog\ProductServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\Form\Exception\TransformationFailedException;

final class ProductTransformerTest extends TestCase
{
    private const EXAMPLE_PRODUCT_CODE = 'KvhvGk3JNzA';

    /** @var \Ibexa\Contracts\ProductCatalog\ProductServiceInterface|\PHPUnit\Framework\MockObject\MockObject */
    private ProductServiceInterface $productService;

    private ProductTransformer $transformer;

    protected function setUp(): void
    {
        $this->productService = $this->createMock(ProductServiceInterface::class);
        $this->transformer = new ProductTransformer($this->productService);
    }

    /**
     * @dataProvider dataProviderForTestTransformDataProvider
     */
    public function testTransform(?ProductInterface $value, ?string $expected): void
    {
        self::assertEquals($expected, $this->transformer->transform($value));
    }

    /**
     * @return iterable<string,mixed>
     */
    public function dataProviderForTestTransformDataProvider(): iterable
    {
        $product = $this->createMock(ProductInterface::class);
        $product->method('getCode')->willReturn(self::EXAMPLE_PRODUCT_CODE);

        yield 'null' => [null, null];
        yield 'product' => [$product, self::EXAMPLE_PRODUCT_CODE];
    }

    public function testTransformWithInvalidInput(): void
    {
        $this->expectException(TransformationFailedException::class);
        $this->expectExceptionMessage('Expected a ' . ProductInterface::class . ' object.');

        $this->transformer->transform(new stdClass());
    }

    public function testReverseTransform(): void
    {
        $product = $this->createMock(ProductInterface::class);

        $this->productService
            ->method('getProduct')
            ->with(self::EXAMPLE_PRODUCT_CODE)
            ->willReturn($product);

        self::assertEquals($product, $this->transformer->reverseTransform(self::EXAMPLE_PRODUCT_CODE));
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

        $this->productService
            ->method('getProduct')
            ->with(self::EXAMPLE_PRODUCT_CODE)
            ->willThrowException($exception);

        $this->transformer->reverseTransform(self::EXAMPLE_PRODUCT_CODE);
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
