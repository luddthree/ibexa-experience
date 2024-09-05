<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\PageBuilder\Block\Mapper;

use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue;
use Ibexa\PageBuilder\Data\Attribute\Attribute;
use Ibexa\PageBuilder\Data\Block\BlockConfiguration;

class BlockValueMapper
{
    public function mapToBlockConfiguration(BlockValue $blockValue): BlockConfiguration
    {
        return new BlockConfiguration(
            $blockValue->getId(),
            $blockValue->getName(),
            $blockValue->getType(),
            $blockValue->getView(),
            $blockValue->getClass(),
            $blockValue->getStyle(),
            $blockValue->getSince(),
            $blockValue->getTill(),
            $this->mapAttributes($blockValue->getAttributes())
        );
    }

    /**
     * @param \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Attribute[] $attributes
     *
     * @return \Ibexa\PageBuilder\Data\Attribute\Attribute[]
     */
    private function mapAttributes(array $attributes): array
    {
        $attributeConfiguration = [];
        foreach ($attributes as $attribute) {
            $attributeName = $attribute->getName();
            $attributeConfiguration[$attributeName] = new Attribute(
                $attribute->getId(),
                $attributeName,
                $attribute->getValue()
            );
        }

        return $attributeConfiguration;
    }
}

class_alias(BlockValueMapper::class, 'EzSystems\EzPlatformPageBuilder\Block\Mapper\BlockValueMapper');
