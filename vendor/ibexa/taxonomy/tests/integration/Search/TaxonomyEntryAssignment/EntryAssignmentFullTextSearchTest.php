<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Taxonomy\Search\TaxonomyEntryAssignment;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Tests\Integration\Taxonomy\Search\AbstractTaxonomyEntrySearchTestCase;

final class EntryAssignmentFullTextSearchTest extends AbstractTaxonomyEntrySearchTestCase
{
    /**
     * @modifiesSearchIndex
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\Exception
     */
    public function testFullTextSearchByTags(): void
    {
        $searchService = self::getSearchService();
        $contentTypeIdentifier = uniqid('tagged_content_type', true);

        $tags = $this->initializeTags();
        $contentType = $this->createContentTypeWithTagsField($contentTypeIdentifier);
        $carsTag = $tags['tags']['cars'];
        $planesTag = $tags['tags']['planes'];
        $content = $this->createContentWithTags($contentType, [$carsTag, $planesTag]);

        $content = $this->contentService->publishVersion($content->getVersionInfo());
        self::ensureSearchIndexIsUpdated();

        $query = new Query();
        $query->filter = new Query\Criterion\LogicalAnd(
            [
                new Query\Criterion\FullText('cars'),
                new Query\Criterion\ContentTypeIdentifier($contentTypeIdentifier),
            ]
        );
        $searchResults = $searchService->findContent($query);

        self::assertSame(1, $searchResults->totalCount);
        $foundContentItem = $searchResults->searchHits[0]->valueObject;
        self::assertInstanceOf(Content::class, $foundContentItem);
        self::assertSame(
            $content->getVersionInfo()->getContentInfo()->getId(),
            $foundContentItem->getVersionInfo()->getContentInfo()->getId()
        );
    }
}
