<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\Event;

final class BlockFragmentRenderEvents
{
    public const FRAGMENT_RENDER = 'ezplatform.ezlandingpage.block.fragment_render';

    /**
     * @param string $blockIdentifier
     *
     * @return string
     */
    public static function getBlockFragmentRenderEventName(string $blockIdentifier): string
    {
        return sprintf(self::FRAGMENT_RENDER . '.%s', $blockIdentifier);
    }
}

class_alias(BlockFragmentRenderEvents::class, 'EzSystems\EzPlatformPageFieldType\Event\BlockFragmentRenderEvents');
