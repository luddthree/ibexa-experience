<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Taxonomy\Search\Legacy\Criterion\Handler;

use Ibexa\Tests\Integration\Taxonomy\Search\AbstractTaxonomyEntrySearchTestCase;

final class TaxonomyEntryIdTest extends AbstractTaxonomyEntrySearchTestCase
{
    /**
     * @modifiesSearchIndex
     */
    public function testHandle(): void
    {
        $tags = $this->initializeTags();
        $rootTag = $tags['tags']['root'];
        $carsTag = $tags['tags']['cars'];
        $planesTag = $tags['tags']['planes'];

        $contentType = $this->createContentTypeWithTagsField('tagged_content');
        $content1 = $this->createContentWithTags($contentType, [$carsTag, $planesTag]);
        $content2 = $this->createContentWithTags($contentType, [$rootTag, $planesTag]);
        $content3 = $this->createContentWithTags($contentType, [$carsTag]);
        self::ensureSearchIndexIsUpdated();

        self::assertTags(
            $rootTag->id,
            0,
            null,
            'Unpublished content should not be included in results.',
        );
        self::assertTags(
            $carsTag->id,
            0,
            null,
            'Unpublished content should not be included in results.',
        );
        self::assertTags(
            $planesTag->id,
            0,
            null,
            'Unpublished content should not be included in results.',
        );

        $content1 = $this->contentService->publishVersion($content1->getVersionInfo());
        $this->contentService->publishVersion($content2->getVersionInfo());
        $this->contentService->publishVersion($content3->getVersionInfo());
        self::ensureSearchIndexIsUpdated();

        self::assertTags($rootTag->id, 1);
        self::assertTags($carsTag->id, 2);
        self::assertTags($planesTag->id, 2);

        $content1 = $this->updateContentWithTags($content1, [$carsTag]);
        self::ensureSearchIndexIsUpdated();

        self::assertTags($rootTag->id, 1);
        self::assertTags(
            $carsTag->id,
            2,
            null,
            'Creating draft should not impact the search results.',
        );
        self::assertTags(
            $planesTag->id,
            2,
            null,
            'Creating draft should not impact the search results.'
        );

        $this->contentService->publishVersion($content1->getVersionInfo());
        self::ensureSearchIndexIsUpdated();

        self::assertTags($rootTag->id, 1);
        self::assertTags($carsTag->id, 2);
        self::assertTags($planesTag->id, 1);

        $content1 = $this->updateContentWithTags($content1, []);
        self::ensureSearchIndexIsUpdated();

        self::assertTags($rootTag->id, 1);
        self::assertTags($carsTag->id, 2);
        self::assertTags($planesTag->id, 1);

        $this->contentService->publishVersion($content1->getVersionInfo());
        self::ensureSearchIndexIsUpdated();

        self::assertTags($rootTag->id, 1);
        self::assertTags($carsTag->id, 1);
        self::assertTags($planesTag->id, 1);
    }
}
