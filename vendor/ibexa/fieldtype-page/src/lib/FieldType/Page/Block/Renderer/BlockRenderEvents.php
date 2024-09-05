<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\FieldType\Page\Block\Renderer;

class BlockRenderEvents
{
    public const GLOBAL_BLOCK_RENDER_PRE = 'ezplatform.ezlandingpage.block.render.pre';
    public const GLOBAL_BLOCK_RENDER_POST = 'ezplatform.ezlandingpage.block.render.post';
    public const BLOCK_RENDER_PRE_PATTERN = 'ezplatform.ezlandingpage.block.render.%s.pre';
    public const BLOCK_RENDER_POST_PATTERN = 'ezplatform.ezlandingpage.block.render.%s.post';

    /**
     * @param string $blockIdentifier
     *
     * @return string
     */
    public static function getBlockPreRenderEventName(string $blockIdentifier)
    {
        return sprintf(self::BLOCK_RENDER_PRE_PATTERN, $blockIdentifier);
    }

    /**
     * @param string $blockIdentifier
     *
     * @return string
     */
    public static function getBlockPostRenderEventName(string $blockIdentifier)
    {
        return sprintf(self::BLOCK_RENDER_POST_PATTERN, $blockIdentifier);
    }
}

class_alias(BlockRenderEvents::class, 'EzSystems\EzPlatformPageFieldType\FieldType\Page\Block\Renderer\BlockRenderEvents');
