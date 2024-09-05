<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Taxonomy\Matcher\TaxonomyEntryBased;

use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry;
use Ibexa\Core\MVC\Symfony\Matcher\ContentBased\MatcherInterface;
use Ibexa\Taxonomy\View\Matcher\TaxonomyEntryBased\Taxonomy;

final class TaxonomyTest extends AbstractMatcherTest
{
    public function dataProviderForTestMatch(): iterable
    {
        yield 'match' => [
            'tag',
            $this->createTaxonomyEntryWithTaxonomy('tag'),
            true,
        ];

        yield 'miss' => [
            'tag',
            $this->createTaxonomyEntryWithTaxonomy('category'),
            false,
        ];
    }

    protected function createMatcher($matchingConfig = null): MatcherInterface
    {
        $matcher = new Taxonomy($this->taxonomyService);
        $matcher->setMatchingConfig($matchingConfig ?? 'default');

        return $matcher;
    }

    private function createTaxonomyEntryWithTaxonomy(string $taxonomy): TaxonomyEntry
    {
        $entry = $this->createMock(TaxonomyEntry::class);
        $entry->method('__get')->with('taxonomy')->willReturn($taxonomy);

        return $entry;
    }
}
