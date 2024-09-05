<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\Extractor;

use Ibexa\ContentForms\Data\Content\ContentUpdateData;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry;
use Ibexa\Taxonomy\Service\TaxonomyConfiguration;

final class TaxonomyEntryExtractor implements TaxonomyEntryExtractorInterface
{
    private TaxonomyConfiguration $taxonomyConfiguration;

    public function __construct(TaxonomyConfiguration $taxonomyConfiguration)
    {
        $this->taxonomyConfiguration = $taxonomyConfiguration;
    }

    public function extractEntryParentFromContentUpdateData(ContentUpdateData $contentUpdateData): ?ContentInfo
    {
        $contentType = $contentUpdateData->contentDraft->getContentType();
        $fieldsData = $contentUpdateData->fieldsData;
        $taxonomyEntry = $this->extractEntryParent($contentType, $fieldsData);

        if (null === $taxonomyEntry) {
            return null;
        }

        return $taxonomyEntry->content->contentInfo;
    }

    /**
     * @param array<\Ibexa\Contracts\ContentForms\Data\Content\FieldData> $fieldsData
     */
    private function extractEntryParent(
        ContentType $contentType,
        array $fieldsData
    ): ?TaxonomyEntry {
        $taxonomy = $this->taxonomyConfiguration->getTaxonomyForContentType($contentType);
        $fieldMappings = $this->taxonomyConfiguration->getFieldMappings($taxonomy);
        $fieldData = $fieldsData[$fieldMappings['parent']];

        return $fieldData->value->getTaxonomyEntry();
    }
}
