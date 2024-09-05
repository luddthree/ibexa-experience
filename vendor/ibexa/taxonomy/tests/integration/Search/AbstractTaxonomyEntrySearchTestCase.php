<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Taxonomy\Search;

use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Contracts\Taxonomy\Service\TaxonomyServiceInterface;
use Ibexa\Solr\Handler as SolrHandler;
use Ibexa\Taxonomy\FieldType\TaxonomyEntryAssignment\Value;
use Ibexa\Tests\Integration\Taxonomy\IbexaKernelTestCase;

abstract class AbstractTaxonomyEntrySearchTestCase extends IbexaKernelTestCase
{
    protected const TAXONOMY_TAGS = 'tags';
    protected const TAXONOMY_PRODUCT_CATEGORIES = 'product_categories';

    protected const CONTENT_TYPE_TAG = 'tag';
    protected const CONTENT_TYPE_PRODUCT_CATEGORY = 'product_category';

    protected ContentService $contentService;

    protected ContentTypeService $contentTypeService;

    protected TaxonomyServiceInterface $taxonomyService;

    protected function setUp(): void
    {
        $this->contentService = self::getContentService();
        $this->contentTypeService = self::getContentTypeService();
        $this->taxonomyService = self::getTaxonomyService();

        self::setAdministratorUser();
    }

    /**
     * @phpstan-return array{
     *     tags: array{
     *         root: \Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry,
     *         cars: \Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry,
     *         planes: \Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry,
     *     },
     *     product_categories: array{
     *         root: \Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry,
     *         laptops: \Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry,
     *         monitors: \Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry,
     *     },
     * }
     */
    protected function initializeTags(): array
    {
        self::ensureSearchIndexIsUpdated();
        $rootTag = $this->taxonomyService->loadEntryByIdentifier('root');
        self::assertTags(
            $rootTag->id,
            0,
            null,
            'Pre-test sanity check failed: Expected empty resultset.',
        );

        $rootProductCategory = self::createTag(null, 'root', self::CONTENT_TYPE_PRODUCT_CATEGORY);
        $laptops = self::createTag($rootProductCategory, 'laptops', self::CONTENT_TYPE_PRODUCT_CATEGORY);
        $monitors = self::createTag($rootProductCategory, 'monitors', self::CONTENT_TYPE_PRODUCT_CATEGORY);

        $carsTag = self::createTag($rootTag, 'cars');
        $planesTag = self::createTag($rootTag, 'planes');

        return [
            'tags' => [
                'root' => $rootTag,
                'cars' => $carsTag,
                'planes' => $planesTag,
            ],
            'product_categories' => [
                'root' => $rootProductCategory,
                'laptops' => $laptops,
                'monitors' => $monitors,
            ],
        ];
    }

    final protected static function ensureSearchIndexIsUpdated(): void
    {
        if (getenv('SEARCH_ENGINE') === 'solr') {
            $handler = self::getServiceByClassName(SolrHandler::class, SolrHandler::class);
            $handler->commit();
        }
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\Exception
     */
    final protected function createContentTypeWithTagsField(string $identifier): ContentType
    {
        $struct = $this->contentTypeService->newContentTypeCreateStruct($identifier);
        $struct->mainLanguageCode = 'eng-GB';
        $struct->names = [
            'eng-GB' => 'Tags test',
        ];

        $struct->addFieldDefinition($this->contentTypeService->newFieldDefinitionCreateStruct(
            'tags',
            'ibexa_taxonomy_entry_assignment',
        ));

        $group = $this->contentTypeService->loadContentTypeGroupByIdentifier('Content');

        $draft = $this->contentTypeService->createContentType($struct, [$group]);
        $this->contentTypeService->publishContentTypeDraft($draft);

        return $this->contentTypeService->loadContentTypeByIdentifier($identifier);
    }

    /**
     * @param array<\Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry> $tags
     */
    final protected function createContentWithTags(ContentType $contentType, array $tags): Content
    {
        $contentCreateStruct = $this->contentService->newContentCreateStruct($contentType, 'eng-GB');
        $contentCreateStruct->setField(
            'tags',
            new Value($tags, 'tags'),
            'eng-GB'
        );

        return $this->contentService->createContent($contentCreateStruct);
    }

    /**
     * @param array<\Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry> $tags
     */
    final protected function updateContentWithTags(Content $content, array $tags): Content
    {
        $content = $this->contentService->createContentDraft($content->contentInfo);
        $struct = $this->contentService->newContentUpdateStruct();
        $struct->setField('tags', new Value($tags, 'tags'));

        return $this->contentService->updateContent($content->getVersionInfo(), $struct);
    }
}
