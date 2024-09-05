<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\Form\DataTransformer;

use Ibexa\Bundle\ProductCatalog\Form\DataTransformer\ProductCategoryCriterionTransformer;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\ProductCategory;
use Ibexa\Contracts\Taxonomy\Service\TaxonomyServiceInterface;
use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\Form\Exception\TransformationFailedException;

final class ProductCategoryCriterionTransformerTest extends TestCase
{
    private ProductCategoryCriterionTransformer $transformer;

    /** @var \Ibexa\Contracts\Taxonomy\Service\TaxonomyServiceInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $taxonomyService;

    protected function setUp(): void
    {
        $this->taxonomyService = $this->createMock(TaxonomyServiceInterface::class);

        $this->transformer = new ProductCategoryCriterionTransformer($this->taxonomyService);
    }

    /**
     * @dataProvider dataProviderForTestTransformDataProvider
     *
     * @param array<\Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry> $expected
     */
    public function testTransform(?ProductCategory $value, array $expected): void
    {
        $this->taxonomyService
            ->method('loadEntryById')
            ->willReturnMap([
                [
                    1, $this->createMock(TaxonomyEntry::class),
                ],
                [
                    2, $this->createMock(TaxonomyEntry::class),
                ],
            ]);

        self::assertEquals($expected, $this->transformer->transform($value));
    }

    /**
     * @return iterable<string,mixed>
     */
    public function dataProviderForTestTransformDataProvider(): iterable
    {
        $productCategoryCriterion = new ProductCategory([1, 2]);

        yield 'null' => [null, []];
        yield 'Product Category Criterion' => [
            $productCategoryCriterion,
            [
                $this->createMock(TaxonomyEntry::class),
                $this->createMock(TaxonomyEntry::class),
            ],
        ];
    }

    public function testTransformWithInvalidInput(): void
    {
        $this->expectException(TransformationFailedException::class);
        $this->expectExceptionMessage(
            'Expected a ' . ProductCategory::class . ' object, received ' . stdClass::class . '.'
        );

        $this->transformer->transform(new stdClass());
    }

    public function testReverseTransform(): void
    {
        $productCategoryCriterion = new ProductCategory([1, 2]);

        self::assertEquals(
            $productCategoryCriterion,
            $this->transformer->reverseTransform([
                $this->getTaxonomyEntryMock(1),
                $this->getTaxonomyEntryMock(2),
            ])
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
        yield 'string' => ['string'];
        yield 'object' => [new stdClass()];
    }

    /**
     * @return \Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry|\PHPUnit\Framework\MockObject\MockObject
     */
    private function getTaxonomyEntryMock(int $id = 1): TaxonomyEntry
    {
        $taxonomyEntry = $this->createMock(TaxonomyEntry::class);
        $taxonomyEntry
            ->method('__get')
            ->with('id')
            ->willReturn($id);

        return $taxonomyEntry;
    }
}
