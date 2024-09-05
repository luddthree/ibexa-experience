<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\FieldType\Page\Block\Relation;

use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Attribute;
use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue;
use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Page;
use Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\BlockAttributeDefinition;

class BlockAttributeTypeRelationExtractorRegistry
{
    /** @var \Ibexa\FieldTypePage\FieldType\Page\Block\Relation\Extractor\BlockAttributeRelationExtractorInterface */
    private $extractors;

    /**
     * @param iterable|\Ibexa\FieldTypePage\FieldType\Page\Block\Relation\Extractor\BlockAttributeRelationExtractorInterface[] $extractors
     */
    public function __construct(iterable $extractors)
    {
        $this->extractors = $extractors;
    }

    /**
     * @param \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Page $page
     * @param \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue $blockValue
     * @param \Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\BlockAttributeDefinition $attributeDefinition
     * @param \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Attribute $attribute
     *
     * @return \Ibexa\FieldTypePage\FieldType\Page\Block\Relation\Extractor\BlockAttributeRelationExtractorInterface[]|iterable
     */
    public function getApplicableExtractors(
        Page $page,
        BlockValue $blockValue,
        BlockAttributeDefinition $attributeDefinition,
        Attribute $attribute
    ): iterable {
        foreach ($this->extractors as $extractor) {
            if (!$extractor->supports($page, $blockValue, $attributeDefinition, $attribute)) {
                continue;
            }

            yield $extractor;
        }
    }
}

class_alias(BlockAttributeTypeRelationExtractorRegistry::class, 'EzSystems\EzPlatformPageFieldType\FieldType\Page\Block\Relation\BlockAttributeTypeRelationExtractorRegistry');
