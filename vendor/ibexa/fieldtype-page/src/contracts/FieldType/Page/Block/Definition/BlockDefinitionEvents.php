<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition;

class BlockDefinitionEvents
{
    public const BLOCK_DEFINITION_PATTERN = 'ezplatform.ezlandingpage.block.definition.%s';
    public const BLOCK_ATTRIBUTE_DEFINITION_PATTERN = 'ezplatform.ezlandingpage.block.definition.%s.attribute.%s';

    /**
     * @param string $blockIdentifier
     *
     * @return string
     */
    public static function getBlockDefinitionEventName(string $blockIdentifier)
    {
        return sprintf(self::BLOCK_DEFINITION_PATTERN, $blockIdentifier);
    }

    /**
     * @param string $blockIdentifier
     * @param string $attributeIdentifier
     *
     * @return string
     */
    public static function getBlockAttributeDefinitionEventName(string $blockIdentifier, string $attributeIdentifier)
    {
        return sprintf(self::BLOCK_ATTRIBUTE_DEFINITION_PATTERN, $blockIdentifier, $attributeIdentifier);
    }
}

class_alias(BlockDefinitionEvents::class, 'EzSystems\EzPlatformPageFieldType\FieldType\Page\Block\Definition\BlockDefinitionEvents');
