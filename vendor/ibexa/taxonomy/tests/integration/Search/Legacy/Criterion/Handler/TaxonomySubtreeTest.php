<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Taxonomy\Search\Legacy\Criterion\Handler;

use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Contracts\Taxonomy\Service\TaxonomyServiceInterface;
use Ibexa\Solr\Handler as SolrHandler;
use Ibexa\Taxonomy\FieldType\TaxonomyEntryAssignment\Value;
use Ibexa\Tests\Integration\Taxonomy\IbexaKernelTestCase;

final class TaxonomySubtreeTest extends IbexaKernelTestCase
{
    private ContentService $contentService;

    private ContentTypeService $contentTypeService;

    private TaxonomyServiceInterface $taxonomyService;

    protected function setUp(): void
    {
        $this->contentService = self::getContentService();
        $this->contentTypeService = self::getContentTypeService();
        $this->taxonomyService = self::getTaxonomyService();

        self::setAdministratorUser();
    }

    private static function ensureSearchIndexIsUpdated(): void
    {
        if (getenv('SEARCH_ENGINE') === 'solr') {
            $handler = self::getServiceByClassName(SolrHandler::class, SolrHandler::class);
            $handler->commit();
        }
    }

    /**
     * @modifiesSearchIndex.
     */
    public function testHandle(): void
    {
        self::ensureSearchIndexIsUpdated();
        $rootTag = $this->taxonomyService->loadEntryByIdentifier('root');
        self::assertSubtreeTags($rootTag->id, 0, 'Pre-test sanity check failed: Expected empty result set.');

        $carsTag = self::createTag($rootTag, 'cars');
        $electricTag = self::createTag($carsTag, 'electric');
        $dieselTag = self::createTag($carsTag, 'diesel');
        $planesTag = self::createTag($rootTag, 'planes');
        $jetTag = self::createTag($planesTag, 'jet');
        $helicopterTag = self::createTag($planesTag, 'helicopter');
        $gliderTag = self::createTag($planesTag, 'glider');
        $airbusTag = self::createTag($jetTag, 'airbus');
        $boeingTag = self::createTag($jetTag, 'boeing');

        $contentType = $this->createContentTypeWithTagsField();
        $content1 = $this->createContentWithTags($contentType, [$carsTag, $planesTag]);
        $content2 = $this->createContentWithTags($contentType, [$rootTag, $planesTag]);
        $content3 = $this->createContentWithTags($contentType, [$electricTag]);
        $content4 = $this->createContentWithTags($contentType, [$dieselTag]);
        $content5 = $this->createContentWithTags($contentType, [$jetTag]);
        $content6 = $this->createContentWithTags($contentType, [$helicopterTag]);
        $content7 = $this->createContentWithTags($contentType, [$gliderTag]);
        $content8 = $this->createContentWithTags($contentType, [$airbusTag]);
        $content9 = $this->createContentWithTags($contentType, [$boeingTag]);
        self::ensureSearchIndexIsUpdated();

        self::assertSubtreeTags($rootTag->id, 0, 'Unpublished content should not be included in results.');
        self::assertSubtreeTags($carsTag->id, 0, 'Unpublished content should not be included in results.');
        self::assertSubtreeTags($planesTag->id, 0, 'Unpublished content should not be included in results.');

        $content1 = $this->contentService->publishVersion($content1->getVersionInfo());
        $this->contentService->publishVersion($content2->getVersionInfo());
        $this->contentService->publishVersion($content3->getVersionInfo());
        $this->contentService->publishVersion($content4->getVersionInfo());
        $this->contentService->publishVersion($content5->getVersionInfo());
        $this->contentService->publishVersion($content6->getVersionInfo());
        $this->contentService->publishVersion($content7->getVersionInfo());
        $this->contentService->publishVersion($content8->getVersionInfo());
        $this->contentService->publishVersion($content9->getVersionInfo());
        self::ensureSearchIndexIsUpdated();

        self::assertSubtreeTags($rootTag->id, 9);
        self::assertSubtreeTags($carsTag->id, 3);
        self::assertSubtreeTags($planesTag->id, 7);

        $content1 = $this->updateContentWithTags($content1, [$carsTag]);
        self::ensureSearchIndexIsUpdated();

        self::assertSubtreeTags($rootTag->id, 9, 'Creating draft should not impact the search results.');
        self::assertSubtreeTags($carsTag->id, 3, 'Creating draft should not impact the search results.');
        self::assertSubtreeTags($planesTag->id, 7, 'Creating draft should not impact the search results.');

        $this->contentService->publishVersion($content1->getVersionInfo());
        self::ensureSearchIndexIsUpdated();

        self::assertSubtreeTags($rootTag->id, 9);
        self::assertSubtreeTags($carsTag->id, 3);
        self::assertSubtreeTags(
            $planesTag->id,
            6,
            'Publishing new version with different tags should impact the search results.'
        );

        $content1 = $this->updateContentWithTags($content1, []);
        self::ensureSearchIndexIsUpdated();

        self::assertSubtreeTags($rootTag->id, 9, 'Creating draft should not impact the search results.');
        self::assertSubtreeTags($carsTag->id, 3, 'Creating draft should not impact the search results.');
        self::assertSubtreeTags($planesTag->id, 6, 'Creating draft should not impact the search results.');

        $this->contentService->publishVersion($content1->getVersionInfo());
        self::ensureSearchIndexIsUpdated();

        self::assertSubtreeTags($rootTag->id, 8);
        self::assertSubtreeTags(
            $carsTag->id,
            2,
            'Publishing new version with different tags should impact the search results.'
        );
        self::assertSubtreeTags($planesTag->id, 6);
    }

    private function createContentTypeWithTagsField(): ContentType
    {
        $struct = $this->contentTypeService->newContentTypeCreateStruct('subtree_tagged_content');
        $struct->mainLanguageCode = 'eng-GB';
        $struct->names = [
            'eng-GB' => 'Tags test',
        ];

        $struct->addFieldDefinition($this->contentTypeService->newFieldDefinitionCreateStruct(
            'subtree-tags',
            'ibexa_taxonomy_entry_assignment',
        ));

        $group = $this->contentTypeService->loadContentTypeGroupByIdentifier('Content');

        $draft = $this->contentTypeService->createContentType($struct, [$group]);
        $this->contentTypeService->publishContentTypeDraft($draft);

        return $this->contentTypeService->loadContentTypeByIdentifier('subtree_tagged_content');
    }

    /**
     * @param array<\Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry> $tags
     */
    private function createContentWithTags(ContentType $contentType, array $tags): Content
    {
        $contentCreateStruct = $this->contentService->newContentCreateStruct($contentType, 'eng-GB');
        $contentCreateStruct->setField(
            'subtree-tags',
            new Value($tags, 'subtree-tags'),
            'eng-GB'
        );

        return $this->contentService->createContent($contentCreateStruct);
    }

    /**
     * @param array<\Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry> $tags
     */
    private function updateContentWithTags(Content $content, array $tags): Content
    {
        $content = $this->contentService->createContentDraft($content->contentInfo);
        $struct = $this->contentService->newContentUpdateStruct();
        $struct->setField('subtree-tags', new Value($tags, 'subtree-tags'));

        return $this->contentService->updateContent($content->getVersionInfo(), $struct);
    }
}
