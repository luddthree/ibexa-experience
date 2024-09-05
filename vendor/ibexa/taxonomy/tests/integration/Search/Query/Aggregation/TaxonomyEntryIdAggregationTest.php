<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Taxonomy\Search\Query\Aggregation;

use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\AggregationResult;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\AggregationResult\TermAggregationResult;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\AggregationResult\TermAggregationResultEntry;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\AggregationResultCollection;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchResult;
use Ibexa\Contracts\Taxonomy\Search\Query\Aggregation\TaxonomyEntryIdAggregation;
use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry;
use Ibexa\Tests\Integration\Taxonomy\Search\AbstractTaxonomyEntrySearchTestCase;

final class TaxonomyEntryIdAggregationTest extends AbstractTaxonomyEntrySearchTestCase
{
    private const AGGREGATION_NAME = 'test_taxonomy_entry_id';

    /**
     * @modifiesSearchIndex
     */
    public function testHandle(): void
    {
        if (getenv('SEARCH_ENGINE') === 'legacy') {
            self::markTestSkipped('Aggregations are not supported on legacy search engine.');
        }

        $tags = $this->initializeTags();
        $rootTag = $tags['tags']['root'];
        $carsTag = $tags['tags']['cars'];
        $planesTag = $tags['tags']['planes'];
        $rootProductCategory = $tags['product_categories']['root'];
        $laptopsProductCategory = $tags['product_categories']['laptops'];
        $monitorsProductCategory = $tags['product_categories']['monitors'];

        $searchResult = self::runSearchWithAggregation(self::TAXONOMY_TAGS);
        self::assertInstanceOf(AggregationResultCollection::class, $searchResult->aggregations);
        self::assertCount(1, $searchResult->aggregations, 'Aggregation count mismatch');

        $aggregation = $searchResult->aggregations->first();
        self::assertTermAggregation(
            $aggregation,
            [],
        );

        $searchResult = self::runSearchWithAggregation(self::TAXONOMY_PRODUCT_CATEGORIES);
        self::assertInstanceOf(AggregationResultCollection::class, $searchResult->aggregations);
        self::assertCount(1, $searchResult->aggregations, 'Aggregation count mismatch');

        $aggregation = $searchResult->aggregations->first();
        self::assertTermAggregation(
            $aggregation,
            [],
        );

        $contentType = $this->createContentTypeWithTagsField('tagged_content');
        $content1 = $this->createContentWithTags($contentType, [$carsTag, $planesTag]);
        $content2 = $this->createContentWithTags($contentType, [$rootTag, $planesTag]);
        $content3 = $this->createContentWithTags($contentType, [$carsTag]);

        $this->contentService->publishVersion($content1->getVersionInfo());
        $this->contentService->publishVersion($content2->getVersionInfo());
        $this->contentService->publishVersion($content3->getVersionInfo());

        $contentType = $this->createContentTypeWithTagsField('content_with_product_categories');
        $content1 = $this->createContentWithTags($contentType, [$laptopsProductCategory, $monitorsProductCategory]);
        $content2 = $this->createContentWithTags($contentType, [$rootProductCategory, $laptopsProductCategory]);
        $content3 = $this->createContentWithTags($contentType, [$monitorsProductCategory]);

        $this->contentService->publishVersion($content1->getVersionInfo());
        $this->contentService->publishVersion($content2->getVersionInfo());
        $this->contentService->publishVersion($content3->getVersionInfo());

        self::ensureSearchIndexIsUpdated();

        $searchResult = self::runSearchWithAggregation(self::TAXONOMY_TAGS);
        self::assertInstanceOf(AggregationResultCollection::class, $searchResult->aggregations);
        self::assertCount(1, $searchResult->aggregations, 'Aggregation count mismatch');

        $aggregation = $searchResult->aggregations->first();
        self::assertTermAggregation(
            $aggregation,
            [
                $rootTag->id => 1,
                $carsTag->id => 2,
                $planesTag->id => 2,
            ],
        );

        $searchResult = self::runSearchWithAggregation(self::TAXONOMY_PRODUCT_CATEGORIES);
        self::assertInstanceOf(AggregationResultCollection::class, $searchResult->aggregations);
        self::assertCount(1, $searchResult->aggregations, 'Aggregation count mismatch');

        $aggregation = $searchResult->aggregations->first();
        self::assertTermAggregation(
            $aggregation,
            [
                $rootProductCategory->id => 1,
                $laptopsProductCategory->id => 2,
                $monitorsProductCategory->id => 2,
            ],
        );
    }

    private static function runSearchWithAggregation(string $taxonomyIdentifier): SearchResult
    {
        $query = new Query();
        $query->aggregations = [
            new TaxonomyEntryIdAggregation(self::AGGREGATION_NAME, $taxonomyIdentifier),
        ];

        return self::getSearchService()->findContent($query);
    }

    /**
     * @param array<int, positive-int> $aggregationResults
     */
    private static function assertTermAggregation(
        AggregationResult $aggregation,
        array $aggregationResults
    ): void {
        self::assertInstanceOf(TermAggregationResult::class, $aggregation);
        self::assertSame(self::AGGREGATION_NAME, $aggregation->getName());

        foreach ($aggregation->getEntries() as $entry) {
            $key = $entry->getKey();

            self::assertInstanceOf(TaxonomyEntry::class, $key);
            self::assertArrayHasKey($key->id, $aggregationResults, 'Unrecognized aggregation entry');
            self::assertTermAggregationKeyCount($aggregationResults[$key->id], $entry);
        }
    }

    private static function assertTermAggregationKeyCount(
        int $expectedCount,
        TermAggregationResultEntry $entry
    ): void {
        self::assertSame(
            $expectedCount,
            $entry->getCount(),
            sprintf('Unexpected count for key: %s', $entry->getKey()->id),
        );
    }
}
