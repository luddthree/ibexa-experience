<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\FieldType\Page\Block\Relation\Extractor;

use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Attribute;
use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue;
use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Page;
use Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\BlockAttributeDefinition;

interface BlockAttributeRelationExtractorInterface
{
    /**
     * @param \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Page $page
     * @param \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue $blockValue
     * @param \Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\BlockAttributeDefinition $attributeDefinition
     * @param \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Attribute $attribute
     *
     * @return bool
     */
    public function supports(
        Page $page,
        BlockValue $blockValue,
        BlockAttributeDefinition $attributeDefinition,
        Attribute $attribute
    ): bool;

    /**
     * @param \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Page $page
     * @param \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue $blockValue
     * @param \Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\BlockAttributeDefinition $attributeDefinition
     * @param \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Attribute $attribute
     *
     * @return array
     */
    public function extract(
        Page $page,
        BlockValue $blockValue,
        BlockAttributeDefinition $attributeDefinition,
        Attribute $attribute
    ): array;
}

class_alias(BlockAttributeRelationExtractorInterface::class, 'EzSystems\EzPlatformPageFieldType\FieldType\Page\Block\Relation\Extractor\BlockAttributeRelationExtractorInterface');
