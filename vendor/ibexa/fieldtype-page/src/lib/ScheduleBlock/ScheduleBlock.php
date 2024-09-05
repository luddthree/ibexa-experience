<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\ScheduleBlock;

use Ibexa\FieldTypePage\ScheduleBlock\Item\Item;
use Ibexa\FieldTypePage\ScheduleBlock\Snapshot\Snapshot;
use Ibexa\FieldTypePage\ScheduleBlock\Timeline\Event\AbstractEvent;

class ScheduleBlock
{
    public const ATTRIBUTE_INITIAL_ITEMS = 'initial_items';
    public const ATTRIBUTE_SNAPSHOTS = 'snapshots';
    public const ATTRIBUTE_EVENTS = 'events';
    public const ATTRIBUTE_LOADED_SNAPSHOT = 'loaded_snapshot';
    public const ATTRIBUTE_SLOTS = 'slots';
    public const ATTRIBUTE_LIMIT = 'limit';

    public const SERIALIZATION_MAP = [
        self::ATTRIBUTE_EVENTS => 'array<' . AbstractEvent::class . '>',
        self::ATTRIBUTE_SLOTS => 'array<string, ' . Slot::class . '>',
        self::ATTRIBUTE_INITIAL_ITEMS => 'array<string, ' . Item::class . '>',
        self::ATTRIBUTE_SNAPSHOTS => 'array<' . Snapshot::class . '>',
        self::ATTRIBUTE_LIMIT => 'int',
        self::ATTRIBUTE_LOADED_SNAPSHOT => Snapshot::class,
    ];
}

class_alias(ScheduleBlock::class, 'EzSystems\EzPlatformPageFieldType\ScheduleBlock\ScheduleBlock');
