<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Taxonomy\Matcher\TaxonomyEntryBased;

use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry;
use Ibexa\Core\MVC\Symfony\Matcher\ContentBased\MatcherInterface;
use Ibexa\Taxonomy\View\Matcher\TaxonomyEntryBased\Identifier;

final class IdentifierTest extends AbstractMatcherTest
{
    public function dataProviderForTestMatch(): iterable
    {
        yield 'match' => [
            ['foo', 'bar', 'baz'],
            $this->createTaxonomyEntryWithIdentifier('foo'),
            true,
        ];

        yield 'match (single value)' => [
            'foo',
            $this->createTaxonomyEntryWithIdentifier('foo'),
            true,
        ];

        yield 'miss' => [
            ['foo', 'bar', 'baz'],
            $this->createTaxonomyEntryWithIdentifier('foobar'),
            false,
        ];

        yield 'miss (single value)' => [
            'foo',
            $this->createTaxonomyEntryWithIdentifier('foobar'),
            false,
        ];

        yield 'empty' => [
            null,
            $this->createTaxonomyEntryWithIdentifier('foobar'),
            false,
        ];
    }

    protected function createMatcher($matchingConfig = null): MatcherInterface
    {
        $matcher = new Identifier($this->taxonomyService);
        $matcher->setMatchingConfig($matchingConfig);

        return $matcher;
    }

    private function createTaxonomyEntryWithIdentifier(string $identifier): TaxonomyEntry
    {
        $entry = $this->createMock(TaxonomyEntry::class);
        $entry->method('__get')->with('identifier')->willReturn($identifier);

        return $entry;
    }
}
