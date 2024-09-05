<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Taxonomy\Matcher\TaxonomyEntryBased;

use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry;
use Ibexa\Core\MVC\Symfony\Matcher\ContentBased\MatcherInterface;
use Ibexa\Taxonomy\View\Matcher\TaxonomyEntryBased\Id;

final class IdTest extends AbstractMatcherTest
{
    private const TAXONOMY_ENTRY_FOO_ID = 1;
    private const TAXONOMY_ENTRY_BAR_ID = 2;
    private const TAXONOMY_ENTRY_BAZ_ID = 3;
    private const TAXONOMY_ENTRY_FOOBAR_ID = 4;

    public function dataProviderForTestMatch(): iterable
    {
        yield 'match' => [
            [
                self::TAXONOMY_ENTRY_FOO_ID,
                self::TAXONOMY_ENTRY_BAR_ID,
                self::TAXONOMY_ENTRY_BAZ_ID,
            ],
            $this->createTaxonomyEntryWithId(self::TAXONOMY_ENTRY_FOO_ID),
            true,
        ];

        yield 'match (single value)' => [
            self::TAXONOMY_ENTRY_FOO_ID,
            $this->createTaxonomyEntryWithId(self::TAXONOMY_ENTRY_FOO_ID),
            true,
        ];

        yield 'miss' => [
            [
                self::TAXONOMY_ENTRY_FOO_ID,
                self::TAXONOMY_ENTRY_BAR_ID,
                self::TAXONOMY_ENTRY_BAZ_ID,
            ],
            $this->createTaxonomyEntryWithId(self::TAXONOMY_ENTRY_FOOBAR_ID),
            false,
        ];

        yield 'miss (single value)' => [
            self::TAXONOMY_ENTRY_FOO_ID,
            $this->createTaxonomyEntryWithId(self::TAXONOMY_ENTRY_FOOBAR_ID),
            false,
        ];

        yield 'empty' => [
            null,
            $this->createTaxonomyEntryWithId(self::TAXONOMY_ENTRY_FOOBAR_ID),
            false,
        ];
    }

    protected function createMatcher($matchingConfig = null): MatcherInterface
    {
        $matcher = new Id($this->taxonomyService);
        $matcher->setMatchingConfig($matchingConfig);

        return $matcher;
    }

    private function createTaxonomyEntryWithId(int $id): TaxonomyEntry
    {
        $entry = $this->createMock(TaxonomyEntry::class);
        $entry->method('__get')->with('id')->willReturn($id);

        return $entry;
    }
}
