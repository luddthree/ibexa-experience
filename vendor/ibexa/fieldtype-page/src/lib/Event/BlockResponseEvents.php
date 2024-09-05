<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\Event;

class BlockResponseEvents
{
    public const BLOCK_RESPONSE = 'ezplatform.ezlandingpage.block.response';

    /**
     * @param string $blockIdentifier
     *
     * @return string
     */
    public static function getBlockResponseEventName(string $blockIdentifier): string
    {
        return sprintf(self::BLOCK_RESPONSE . '.%s', $blockIdentifier);
    }
}

class_alias(BlockResponseEvents::class, 'EzSystems\EzPlatformPageFieldType\Event\BlockResponseEvents');
