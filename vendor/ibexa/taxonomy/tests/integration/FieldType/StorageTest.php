<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Taxonomy\FieldType;

use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Taxonomy\Service\TaxonomyServiceInterface;
use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry;
use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntryCreateStruct;
use Ibexa\Core\Repository\Values\Content\ContentCreateStruct;
use Ibexa\Core\Repository\Values\Content\ContentUpdateStruct;
use Ibexa\Taxonomy\FieldType\TaxonomyEntryAssignment\Value;
use Ibexa\Taxonomy\Service\TaxonomyConfiguration;
use Ibexa\Tests\Integration\Taxonomy\IbexaKernelTestCase;
use Ibexa\Tests\Taxonomy\ContentTestDataProvider;
use Ibexa\Tests\Taxonomy\FieldTestDataProvider;

final class StorageTest extends IbexaKernelTestCase
{
    private const GER_LANGUAGE_CODE = 'ger-DE';

    private ContentTypeService $contentTypeService;

    private ContentService $contentService;

    private TaxonomyServiceInterface $taxonomyService;

    private TaxonomyConfiguration $taxonomyConfiguration;

    protected function setUp(): void
    {
        parent::setUp();
        self::setAdministratorUser();

        $this->contentTypeService = self::getContentTypeService();
        $this->contentService = self::getContentService();
        $this->taxonomyService = self::getTaxonomyService();
        $this->taxonomyConfiguration = self::getTaxonomyConfiguration();
    }

    public function testPublishContentVersionWithDifferentTagsThanOriginalTranslation(): void
    {
        [$entry1, $entry2] = $this->createTagTaxonomyEntries();
        $content = $this->createContentWithSingleTaxonomyEntryAssignment($entry1);

        $contentDraft = $this->contentService->createContentDraft($content->contentInfo);

        $updateStruct = new ContentUpdateStruct();
        $updateStruct->initialLanguageCode = self::GER_LANGUAGE_CODE;
        $this->contentService->updateContent(
            $contentDraft->getVersionInfo(),
            $updateStruct,
        );

        $updateStruct = new ContentUpdateStruct();
        $updateStruct->setField('name', 'GER name', self::GER_LANGUAGE_CODE);
        $updateStruct->setField(
            'tags',
            new Value([$entry1, $entry2], 'tags'),
            self::GER_LANGUAGE_CODE
        );

        $updatedDraftContent = $this->contentService->updateContent(
            $contentDraft->getVersionInfo(),
            $updateStruct,
        );
        $publishedContent = $this->contentService->publishVersion($updatedDraftContent->getVersionInfo());

        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Field $taxonomyEntryAssignmentField */
        $taxonomyEntryAssignmentField = $publishedContent->getField('tags', 'ger-DE');
        $taxonomyEntries = $taxonomyEntryAssignmentField->getValue()->getTaxonomyEntries();
        $taxonomyEntriesIdentifiers = array_column($taxonomyEntries, 'identifier');

        self::assertContains($entry1->getIdentifier(), $taxonomyEntriesIdentifiers);
        self::assertContains($entry2->getIdentifier(), $taxonomyEntriesIdentifiers);
    }

    /**
     * @return array<\Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry>
     */
    private function createTagTaxonomyEntries(): array
    {
        $tagContentType = $this->contentTypeService->loadContentTypeByIdentifier('tag');
        $taxonomy = $this->taxonomyConfiguration->getTaxonomyForContentType($tagContentType);
        $rootEntry = $this->taxonomyService->loadEntryByIdentifier('root');
        $content = ContentTestDataProvider::getContent($tagContentType, FieldTestDataProvider::getFields($rootEntry));

        $createStruct1 = new TaxonomyEntryCreateStruct(
            'test',
            $taxonomy,
            $rootEntry,
            $content
        );
        $createStruct2 = new TaxonomyEntryCreateStruct(
            'test2',
            $taxonomy,
            $rootEntry,
            $content
        );

        $entry1 = $this->taxonomyService->createEntry($createStruct1);
        $entry2 = $this->taxonomyService->createEntry($createStruct2);

        return [$entry1, $entry2];
    }

    private function createContentWithSingleTaxonomyEntryAssignment(TaxonomyEntry $entry): Content
    {
        $contentType = $this->contentTypeService->loadContentTypeByIdentifier('tagged_folder');

        $contentCreateStruct = new ContentCreateStruct(
            [
                'mainLanguageCode' => 'eng-GB',
                'contentType' => $contentType,
                'alwaysAvailable' => true,
            ]
        );
        $contentCreateStruct->setField('name', 'Name');
        $contentCreateStruct->setField(
            'tags',
            new Value([$entry], 'tags')
        );

        $draft = $this->contentService->createContent($contentCreateStruct);

        return $this->contentService->publishVersion($draft->getVersionInfo());
    }
}
