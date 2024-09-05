<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\Form\DataTransformer;

use Ibexa\Bundle\ProductCatalog\Form\DataTransformer\ProductTypeCriterionTransformer;
use Ibexa\Contracts\ProductCatalog\ProductTypeServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\ProductType;
use Ibexa\Contracts\ProductCatalog\Values\ProductTypeInterface;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\Form\Exception\TransformationFailedException;

final class ProductTypeCriterionTransformerTest extends TestCase
{
    /** @var \Ibexa\Contracts\ProductCatalog\ProductTypeServiceInterface|\PHPUnit\Framework\MockObject\MockObject */
    private ProductTypeServiceInterface $productTypeService;

    private ProductTypeCriterionTransformer $transformer;

    protected function setUp(): void
    {
        $this->productTypeService = $this->createMock(ProductTypeServiceInterface::class);
        $this->transformer = new ProductTypeCriterionTransformer($this->productTypeService);
    }

    /**
     * @param \Ibexa\Contracts\ProductCatalog\Values\ProductTypeInterface[] $expected
     *
     * @dataProvider dataProviderForTestTransformDataProvider
     */
    public function testTransform(?ProductType $value, array $expected): void
    {
        $productType = $this->createMock(ProductTypeInterface::class);
        $this->productTypeService
            ->method('getProductType')
            ->with('foo')
            ->willReturn($productType);

        self::assertEquals($expected, $this->transformer->transform($value));
    }

    /**
     * @return iterable<string,mixed>
     */
    public function dataProviderForTestTransformDataProvider(): iterable
    {
        $productType = $this->createMock(ProductTypeInterface::class);

        $productTypeCriterion = new ProductType(['foo']);

        yield 'null' => [null, []];
        yield 'Product Type Criterion' => [$productTypeCriterion, [$productType]];
    }

    public function testTransformWithInvalidInput(): void
    {
        $this->expectException(TransformationFailedException::class);
        $this->expectExceptionMessage('Expected a ' . ProductType::class . ' object.');

        $this->transformer->transform(new stdClass());
    }

    public function testReverseTransform(): void
    {
        $productType = $this->createMock(ProductTypeInterface::class);
        $productType->method('getIdentifier')->willReturn('foo');

        self::assertEquals(
            new ProductType(['foo']),
            $this->transformer->reverseTransform([$productType])
        );
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
        yield 'string' => ['element'];
        yield 'object' => [new stdClass()];
    }
}
