<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Scheduler\Calendar;

use Ibexa\Contracts\Calendar\EventType\EventTypeInterface;
use Ibexa\Contracts\Scheduler\Repository\DateBasedHideServiceInterface;
use Ibexa\Contracts\Scheduler\ValueObject\ScheduledEntry;

final class FutureHideEventSource extends AbstractDateBasedActionEventSource
{
    public function __construct(
        DateBasedHideServiceInterface $dateBasedService,
        EventTypeInterface $eventType
    ) {
        parent::__construct($dateBasedService, $eventType);
    }

    protected function buildEventDomainObject(
        EventTypeInterface $eventType,
        string $id,
        ScheduledEntry $scheduledscheduledEntry
    ): ScheduledEntryBasedEvent {
        return new FutureHideEvent(
            $eventType,
            $id,
            $scheduledscheduledEntry,
        );
    }
}

class_alias(FutureHideEventSource::class, 'EzSystems\DateBasedPublisher\Core\Calendar\FutureHideEventSource');
