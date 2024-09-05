<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\Repository\Content;

use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Taxonomy\Service\TaxonomyServiceInterface;
use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry;
use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntryUpdateStruct;
use Ibexa\Core\Repository\Values\Content\ContentUpdateStruct;
use Ibexa\Taxonomy\FieldType\TaxonomyEntry\Value;
use Ibexa\Taxonomy\Service\TaxonomyConfiguration;

/**
 * @internal
 */
final class ContentSynchronizer implements ContentSynchronizerInterface
{
    private TaxonomyConfiguration $taxonomyConfiguration;

    private TaxonomyServiceInterface $taxonomyService;

    private ContentService $contentService;

    public function __construct(
        TaxonomyConfiguration $taxonomyConfiguration,
        TaxonomyServiceInterface $taxonomyService,
        ContentService $contentService
    ) {
        $this->taxonomyConfiguration = $taxonomyConfiguration;
        $this->taxonomyService = $taxonomyService;
        $this->contentService = $contentService;
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function synchronize(TaxonomyEntry $taxonomyEntry): void
    {
        // ensure we use published version
        $content = $this->contentService->loadContent($taxonomyEntry->content->id);

        $updateStruct = $this->createUpdateStructFromContent($content);

        $this->taxonomyService->updateEntry($taxonomyEntry, $updateStruct);
    }

    public function reverseSynchronize(TaxonomyEntry $taxonomyEntry): void
    {
        // ensure we use published version
        $content = $this->contentService->loadContent($taxonomyEntry->content->id);

        $contentType = $content->getContentType();

        $taxonomy = $this->taxonomyConfiguration->getTaxonomyForContentType($contentType);
        $taxonomyConfig = $this->taxonomyConfiguration->getFieldMappings($taxonomy);
        $parentField = $content->getField($taxonomyConfig['parent']);

        if ($parentField === null) {
            return;
        }

        $updateStruct = new ContentUpdateStruct();
        $updateStruct->setField($parentField->getFieldDefinitionIdentifier(), new Value($taxonomyEntry->getParent()));

        $draft = $this->contentService->createContentDraft($content->getVersionInfo()->getContentInfo());
        $this->contentService->updateContent($draft->getVersionInfo(), $updateStruct);
        $this->contentService->publishVersion($draft->getVersionInfo());
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    private function createUpdateStructFromContent(Content $content): TaxonomyEntryUpdateStruct
    {
        $contentType = $content->getContentType();

        $taxonomy = $this->taxonomyConfiguration->getTaxonomyForContentType($contentType);
        $taxonomyConfig = $this->taxonomyConfiguration->getFieldMappings($taxonomy);

        $identifierField = $content->getField($taxonomyConfig['identifier']);
        $nameField = $content->getField($taxonomyConfig['name']);
        $parentField = $content->getField($taxonomyConfig['parent']);

        /** @var \Ibexa\Core\FieldType\TextLine\Value $identifierValue */
        $identifierValue = $identifierField->value;

        /** @var \Ibexa\Core\FieldType\TextLine\Value $nameValue */
        $nameValue = $nameField->value;

        /** @var \Ibexa\Taxonomy\FieldType\TaxonomyEntry\Value $parentValue */
        $parentValue = $parentField->value;

        return new TaxonomyEntryUpdateStruct(
            $identifierValue->text,
            $nameValue->text,
            $content->getVersionInfo()->getNames(),
            $content->getDefaultLanguageCode(),
            $content->getDefaultLanguageCode(),
            $parentValue->getTaxonomyEntry(),
            $content
        );
    }
}
