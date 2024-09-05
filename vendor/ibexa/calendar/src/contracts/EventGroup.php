<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Calendar;

/**
 * Group of events within given date range e.g. today's agenda.
 */
final class EventGroup extends EventList
{
    public function getGroupKey(): DateRange
    {
        return $this->getQuery()->getDateRange();
    }

    public static function fromEventList(EventList $eventList): self
    {
        return new self($eventList->getQuery(), $eventList->getEvents(), $eventList->getTotalCount());
    }
}

class_alias(EventGroup::class, 'EzSystems\EzPlatformCalendar\Calendar\EventGroup');
