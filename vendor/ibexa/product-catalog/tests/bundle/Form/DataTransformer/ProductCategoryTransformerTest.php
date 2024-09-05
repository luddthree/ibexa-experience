<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\Form\DataTransformer;

use Ibexa\Bundle\ProductCatalog\Form\DataTransformer\ProductCategoryTransformer;
use Ibexa\Contracts\Taxonomy\Service\TaxonomyServiceInterface;
use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry;
use Ibexa\Core\Repository\Values\Content\Content;
use Ibexa\Taxonomy\Exception\TaxonomyEntryNotFoundException;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\Form\Exception\TransformationFailedException;

final class ProductCategoryTransformerTest extends TestCase
{
    private const EXAMPLE_TAXONOMY_ENTRY_ID = 1;

    /** @var \Ibexa\Contracts\Taxonomy\Service\TaxonomyServiceInterface|\Ibexa\Contracts\Taxonomy\Service\TaxonomyServiceInterface|\PHPUnit\Framework\MockObject\MockObject */
    private TaxonomyServiceInterface $taxonomyService;

    private ProductCategoryTransformer $transformer;

    protected function setUp(): void
    {
        $this->taxonomyService = $this->createMock(TaxonomyServiceInterface::class);
        $this->transformer = new ProductCategoryTransformer($this->taxonomyService);
    }

    /**
     * @dataProvider dataProviderForTestTransformDataProvider
     */
    public function testTransform(?TaxonomyEntry $value, ?int $expected): void
    {
        self::assertEquals($expected, $this->transformer->transform($value));
    }

    /**
     * @return iterable<string,mixed>
     */
    public function dataProviderForTestTransformDataProvider(): iterable
    {
        yield 'null' => [null, null];

        yield 'taxonomy entry' => [
            $this->getExampleTaxonomyEntry(self::EXAMPLE_TAXONOMY_ENTRY_ID),
            self::EXAMPLE_TAXONOMY_ENTRY_ID,
        ];
    }

    public function testTransformWithInvalidInput(): void
    {
        $this->expectException(TransformationFailedException::class);
        $this->expectExceptionMessage('Expected an Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry, received stdClass.');

        $this->transformer->transform(new stdClass());
    }

    public function testReverseTransform(): void
    {
        $expectedTaxonomyEntry = $this->getExampleTaxonomyEntry(self::EXAMPLE_TAXONOMY_ENTRY_ID);

        $this->taxonomyService
            ->method('loadEntryById')
            ->with(self::EXAMPLE_TAXONOMY_ENTRY_ID)
            ->willReturn($expectedTaxonomyEntry);

        self::assertSame(
            $expectedTaxonomyEntry,
            $this->transformer->reverseTransform((string)self::EXAMPLE_TAXONOMY_ENTRY_ID)
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
        yield 'bool' => [true];
        yield 'string' => ['string'];
        yield 'object' => [new stdClass()];
    }

    public function testReverseTransformWithNonExistingTaxonomyEntry(): void
    {
        $this->expectException(TransformationFailedException::class);

        $this->taxonomyService
            ->method('loadEntryById')
            ->with(self::EXAMPLE_TAXONOMY_ENTRY_ID)
            ->willThrowException(
                TaxonomyEntryNotFoundException::createWithId(self::EXAMPLE_TAXONOMY_ENTRY_ID)
            );

        $this->transformer->reverseTransform(self::EXAMPLE_TAXONOMY_ENTRY_ID);
    }

    private function getExampleTaxonomyEntry(int $id): TaxonomyEntry
    {
        return new TaxonomyEntry(
            $id,
            'example',
            'Example',
            'eng-GB',
            ['eng-GB' => 'Example'],
            null,
            $this->createMock(Content::class),
            'category'
        );
    }
}
