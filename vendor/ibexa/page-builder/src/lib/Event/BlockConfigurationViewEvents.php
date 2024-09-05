<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\PageBuilder\Event;

class BlockConfigurationViewEvents
{
    public const GLOBAL_BLOCK_CONFIGURATION_VIEW = 'ezplatform.page.block.configuration.view';
    public const BLOCK_CONFIGURATION_VIEW_PATTERN = 'ezplatform.page.block.configuration.view.%s';

    public static function getBlockConfigurationViewEventName(string $blockIdentifier)
    {
        return sprintf(self::BLOCK_CONFIGURATION_VIEW_PATTERN, $blockIdentifier);
    }
}

class_alias(BlockConfigurationViewEvents::class, 'EzSystems\EzPlatformPageBuilder\Event\BlockConfigurationViewEvents');
