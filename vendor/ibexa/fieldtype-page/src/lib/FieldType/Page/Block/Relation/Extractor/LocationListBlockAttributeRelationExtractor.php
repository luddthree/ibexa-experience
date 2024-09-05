<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\FieldType\Page\Block\Relation\Extractor;

use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Attribute;
use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue;
use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Page;
use Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\BlockAttributeDefinition;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;
use Ibexa\Core\Repository\Mapper\ContentLocationMapper\ContentLocationMapper;

class LocationListBlockAttributeRelationExtractor implements BlockAttributeRelationExtractorInterface
{
    /** @var \Ibexa\Contracts\Core\Repository\LocationService */
    private $locationService;

    /** @var \Ibexa\Core\Repository\Mapper\ContentLocationMapper\ContentLocationMapper */
    private $contentLocationMapper;

    public function __construct(
        LocationService $locationService,
        ContentLocationMapper $contentLocationMapper
    ) {
        $this->locationService = $locationService;
        $this->contentLocationMapper = $contentLocationMapper;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(
        Page $page,
        BlockValue $blockValue,
        BlockAttributeDefinition $attributeDefinition,
        Attribute $attribute
    ): bool {
        return 'locationlist' === $attributeDefinition->getType();
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     *
     * @return array<int>
     */
    public function extract(
        Page $page,
        BlockValue $blockValue,
        BlockAttributeDefinition $attributeDefinition,
        Attribute $attribute
    ): array {
        $value = $attribute->getValue();
        if (empty($value)) {
            return [];
        }
        if (!is_int($value) && !is_string($value)) {
            throw new InvalidArgumentException(
                '$attribute',
                sprintf(
                    'Location list page block attribute value must be either a comma separated list of location IDs or ' .
                    'a single location ID, %s given',
                    get_debug_type($value)
                )
            );
        }

        $locationIds = explode(',', (string)$value);
        $contentIds = [];
        $locationsWithMissingContentIds = [];

        foreach ($locationIds as $locationId) {
            $locationId = (int) $locationId;
            if (!$this->contentLocationMapper->hasMapping($locationId)) {
                $locationsWithMissingContentIds[] = $locationId;

                continue;
            }

            $contentIds[] = $this->contentLocationMapper->getMapping($locationId);
        }

        if (!empty($locationsWithMissingContentIds)) {
            $contentIds = array_merge(
                $contentIds,
                array_column(
                    $this->locationService->loadLocationList($locationsWithMissingContentIds),
                    'contentId'
                )
            );
        }

        return array_unique($contentIds);
    }
}

class_alias(LocationListBlockAttributeRelationExtractor::class, 'EzSystems\EzPlatformPageFieldType\FieldType\Page\Block\Relation\Extractor\LocationListBlockAttributeRelationExtractor');
