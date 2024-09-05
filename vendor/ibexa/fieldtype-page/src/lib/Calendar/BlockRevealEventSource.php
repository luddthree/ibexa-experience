<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\Calendar;

use Ibexa\Contracts\Calendar\Event;
use Ibexa\Contracts\Calendar\EventType\EventTypeInterface;

final class BlockRevealEventSource extends AbstractFromProviderEventSource
{
    /**
     * @param \Ibexa\FieldTypePage\Calendar\ScheduledVisibilityEntry|\Ibexa\FieldTypePage\Calendar\ScheduledEntryInterface $scheduledEntry
     */
    protected function buildEvent(
        EventTypeInterface $eventType,
        ScheduledEntryInterface $scheduledEntry,
        string $eventId
    ): Event {
        return new BlockRevealEvent(
            $eventType,
            $eventId,
            $scheduledEntry->getDate(),
            $scheduledEntry->getLanguage(),
            $scheduledEntry->getUser(),
            $scheduledEntry->getContent(),
            $scheduledEntry->getBlockName(),
            $scheduledEntry->getBlockType()
        );
    }
}

class_alias(BlockRevealEventSource::class, 'EzSystems\EzPlatformPageFieldType\Calendar\BlockRevealEventSource');
