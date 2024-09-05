<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\PageBuilder\Block\Mapper;

use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Attribute;
use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue;
use Ibexa\PageBuilder\Data\Block\BlockConfiguration;

class BlockConfigurationMapper
{
    /**
     * @param \Ibexa\PageBuilder\Data\Block\BlockConfiguration $blockConfiguration
     *
     * @return \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue
     */
    public function mapToBlockValue(BlockConfiguration $blockConfiguration): BlockValue
    {
        return new BlockValue(
            $blockConfiguration->getId(),
            $blockConfiguration->getType(),
            $blockConfiguration->getName(),
            $blockConfiguration->getView(),
            $blockConfiguration->getClass(),
            $blockConfiguration->getStyle(),
            null,
            $blockConfiguration->getSince(),
            $blockConfiguration->getTill(),
            $this->mapAttributes($blockConfiguration->getAttributes())
        );
    }

    /**
     * @param \Ibexa\PageBuilder\Data\Attribute\Attribute[] $attributes
     *
     * @return \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Attribute[]
     */
    private function mapAttributes(array $attributes): array
    {
        $mappedAttributes = [];
        foreach ($attributes as $attribute) {
            $mappedAttributes[] = new Attribute($attribute->getId(), $attribute->getName(), $attribute->getValue());
        }

        return $mappedAttributes;
    }
}

class_alias(BlockConfigurationMapper::class, 'EzSystems\EzPlatformPageBuilder\Block\Mapper\BlockConfigurationMapper');
