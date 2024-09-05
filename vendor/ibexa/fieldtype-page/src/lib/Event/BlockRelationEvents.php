<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\Event;

class BlockRelationEvents
{
    public const COLLECT_BLOCK_RELATIONS = 'ezplatform.ezlandingpage.block.relation';

    /**
     * @param string $blockIdentifier
     *
     * @return string
     */
    public static function getCollectBlockRelationsEventName(string $blockIdentifier): string
    {
        return sprintf(self::COLLECT_BLOCK_RELATIONS . '.%s', $blockIdentifier);
    }
}

class_alias(BlockRelationEvents::class, 'EzSystems\EzPlatformPageFieldType\Event\BlockRelationEvents');
