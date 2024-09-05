<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Taxonomy\Form\DataTransformer;

use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry;
use Ibexa\Taxonomy\Form\DataTransformer\TaxonomyEntryDataTransformer;
use stdClass;
use Symfony\Component\Form\Exception\TransformationFailedException;

final class TaxonomyEntryDataTransformerTest extends AbstractTaxonomyEntryTransformerTest
{
    private TaxonomyEntryDataTransformer $transformer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->transformer = new TaxonomyEntryDataTransformer($this->taxonomyService);
    }

    /**
     * @dataProvider dataProviderForTestTransformDataProvider
     */
    public function testTransform(?TaxonomyEntry $value, ?int $expected): void
    {
        self::assertEquals($expected, $this->transformer->transform($value));
    }

    /**
     * @return iterable<string, mixed>
     */
    public function dataProviderForTestTransformDataProvider(): iterable
    {
        yield 'null' => [null, null];
        yield 'entry' => [$this->createEntry(1), 1];
    }

    public function testTransformWithInvalidInput(): void
    {
        $this->expectException(TransformationFailedException::class);
        $this->expectExceptionMessage(sprintf(
            'Value of type %s expected',
            TaxonomyEntry::class
        ));

        /** @phpstan-ignore-next-line */
        $this->transformer->transform(new stdClass());
    }

    /**
     * @dataProvider dataProviderForTestReverseTransformDataProvider
     */
    public function testReverseTransform(?int $value, ?TaxonomyEntry $expected): void
    {
        self::assertEquals($expected, $this->transformer->reverseTransform($value));
    }

    /**
     * @return iterable<string, mixed>
     */
    public function dataProviderForTestReverseTransformDataProvider(): iterable
    {
        yield 'null' => [null, null];
        yield 'entry' => [1, $this->createEntry(1)];
    }
}
