<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Taxonomy\Form\DataTransformer;

use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry;
use Ibexa\Taxonomy\Form\DataTransformer\EntryListDataTransformer;
use stdClass;
use Symfony\Component\Form\Exception\TransformationFailedException;

final class EntryListDataTransformerTest extends AbstractTaxonomyEntryTransformerTest
{
    private EntryListDataTransformer $transformer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->transformer = new EntryListDataTransformer($this->taxonomyService);
    }

    /**
     * @dataProvider dataProviderForTestTransformDataProvider
     *
     * @param array<\Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry>|null $value
     */
    public function testTransform(?array $value, string $expected): void
    {
        self::assertEquals($expected, $this->transformer->transform($value));
    }

    /**
     * @return iterable<string, mixed>
     */
    public function dataProviderForTestTransformDataProvider(): iterable
    {
        $entry1 = $this->createEntry(1);
        $entry2 = $this->createEntry(2);
        $entry3 = $this->createEntry(3);

        yield 'null' => [null, ''];
        yield 'empty array' => [[], ''];
        yield 'array' => [[$entry1, $entry2, $entry3], '1,2,3'];
    }

    public function testTransformWithInvalidInput(): void
    {
        $this->expectException(TransformationFailedException::class);
        $this->expectExceptionMessage(sprintf(
            'Invalid data. Array of %s elements expected.',
            TaxonomyEntry::class,
        ));

        /** @phpstan-ignore-next-line */
        $this->transformer->transform(new stdClass());
    }

    /**
     * @dataProvider dataProviderForTestReverseTransformDataProvider
     *
     * @param array<\Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry> $expected
     */
    public function testReverseTransform(?string $value, array $expected): void
    {
        self::assertEquals($expected, $this->transformer->reverseTransform($value));
    }

    /**
     * @return iterable<string, mixed>
     */
    public function dataProviderForTestReverseTransformDataProvider(): iterable
    {
        $entry1 = $this->createEntry(1);
        $entry2 = $this->createEntry(2);
        $entry3 = $this->createEntry(3);

        yield 'null' => [null, []];
        yield 'single entry' => ['1', [$entry1]];
        yield 'comma separated list' => ['1,2,3', [$entry1, $entry2, $entry3]];
    }
}
