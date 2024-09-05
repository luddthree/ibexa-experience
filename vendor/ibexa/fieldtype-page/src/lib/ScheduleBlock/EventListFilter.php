<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\ScheduleBlock;

use DateTimeInterface;
use Ibexa\Contracts\FieldTypePage\ScheduleBlock\Timeline\Event\EventInterface;

/**
 * @internal
 */
class EventListFilter
{
    /**
     * @param \Ibexa\Contracts\FieldTypePage\ScheduleBlock\Timeline\Event\EventInterface[] $events
     * @param \DateTimeInterface $date
     *
     * @return \Ibexa\Contracts\FieldTypePage\ScheduleBlock\Timeline\Event\EventInterface[]
     */
    public function getEventsAfterDate(array $events, DateTimeInterface $date): array
    {
        return array_filter(
            $events,
            static function (EventInterface $event) use ($date) {
                return $event->getDateTime() > $date;
            }
        );
    }
}

class_alias(EventListFilter::class, 'EzSystems\EzPlatformPageFieldType\ScheduleBlock\EventListFilter');
