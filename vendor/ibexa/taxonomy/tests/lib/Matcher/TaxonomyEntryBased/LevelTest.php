<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Taxonomy\Matcher\TaxonomyEntryBased;

use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry;
use Ibexa\Core\MVC\Symfony\Matcher\ContentBased\MatcherInterface;
use Ibexa\Taxonomy\View\Matcher\TaxonomyEntryBased\Level;

final class LevelTest extends AbstractMatcherTest
{
    public function dataProviderForTestMatch(): iterable
    {
        yield 'eq match' => [
            3,
            $this->createTaxonomyEntryWithLevel(3),
            true,
        ];

        yield 'eq match (explicit operator)' => [
            '= 3',
            $this->createTaxonomyEntryWithLevel(3),
            true,
        ];

        yield 'eq miss' => [
            3,
            $this->createTaxonomyEntryWithLevel(5),
            false,
        ];

        yield 'gt match' => [
            '> 3',
            $this->createTaxonomyEntryWithLevel(4),
            true,
        ];

        yield 'gte match' => [
            '>= 3',
            $this->createTaxonomyEntryWithLevel(4),
            true,
        ];

        yield 'gte match (edge)' => [
            '>= 3',
            $this->createTaxonomyEntryWithLevel(3),
            true,
        ];

        yield 'lt match' => [
            '< 3',
            $this->createTaxonomyEntryWithLevel(1),
            true,
        ];

        yield 'lte match' => [
            '<= 3',
            $this->createTaxonomyEntryWithLevel(1),
            true,
        ];

        yield 'lte match (edge)' => [
            '<= 3',
            $this->createTaxonomyEntryWithLevel(3),
            true,
        ];
    }

    protected function createMatcher($matchingConfig = null): MatcherInterface
    {
        $matcher = new Level($this->taxonomyService);
        $matcher->setMatchingConfig($matchingConfig ?? -1);

        return $matcher;
    }

    private function createTaxonomyEntryWithLevel(int $level): TaxonomyEntry
    {
        $entry = $this->createMock(TaxonomyEntry::class);
        $entry->method('__get')->with('level')->willReturn($level);

        return $entry;
    }
}
