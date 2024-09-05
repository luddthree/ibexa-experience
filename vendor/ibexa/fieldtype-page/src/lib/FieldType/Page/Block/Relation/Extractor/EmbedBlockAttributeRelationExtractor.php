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

class EmbedBlockAttributeRelationExtractor implements BlockAttributeRelationExtractorInterface
{
    /** @var string[] */
    private $attributeIdentifiers;

    /**
     * @param string[] $attributeIdentifiers
     */
    public function __construct(array $attributeIdentifiers = [])
    {
        $this->attributeIdentifiers = $attributeIdentifiers;
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
        return \in_array($attributeDefinition->getType(), $this->attributeIdentifiers, true);
    }

    /**
     * {@inheritdoc}
     */
    public function extract(
        Page $page,
        BlockValue $blockValue,
        BlockAttributeDefinition $attributeDefinition,
        Attribute $attribute
    ): array {
        return [(int)$attribute->getValue()];
    }
}

class_alias(EmbedBlockAttributeRelationExtractor::class, 'EzSystems\EzPlatformPageFieldType\FieldType\Page\Block\Relation\Extractor\EmbedBlockAttributeRelationExtractor');
