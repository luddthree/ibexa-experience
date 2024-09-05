<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Calendar;

use DateInterval;
use Ibexa\Calendar\Exception\UnsupportedActionException;
use Ibexa\Contracts\Calendar\CalendarServiceInterface;
use Ibexa\Contracts\Calendar\DateRange;
use Ibexa\Contracts\Calendar\EventAction\EventActionContext;
use Ibexa\Contracts\Calendar\EventCollection;
use Ibexa\Contracts\Calendar\EventGroup;
use Ibexa\Contracts\Calendar\EventGroupList;
use Ibexa\Contracts\Calendar\EventList;
use Ibexa\Contracts\Calendar\EventQuery;
use LimitIterator;

final class CalendarService implements CalendarServiceInterface
{
    /** @var \Ibexa\Contracts\Calendar\EventSource\EventSourceInterface[] */
    private $sources;

    /**
     * @param \Ibexa\Contracts\Calendar\EventSource\EventSourceInterface[] $sources
     */
    public function __construct(iterable $sources)
    {
        $this->sources = $sources;
    }

    public function executeAction(EventActionContext $context): void
    {
        $events = $context->getEvents();
        if ($events->isEmpty()) {
            return;
        }

        $type = $events->first()->getType();
        foreach ($type->getActions() as $action) {
            if ($action->supports($context)) {
                $action->execute($context);

                return;
            }
        }

        throw new UnsupportedActionException('Unsupported action definition found for: ' . get_class($context));
    }

    /**
     * @param string[] $eventsIds
     */
    public function loadEvents(array $eventsIds): EventCollection
    {
        $merged = new EventHeap();
        foreach ($this->sources as $source) {
            foreach ($source->loadEvents($eventsIds) as $event) {
                $merged->insert($event);
            }
        }

        return EventCollection::fromIterator($merged);
    }

    public function getEvents(EventQuery $query): EventList
    {
        $totalCount = 0;

        $merged = new EventHeap();
        foreach ($this->sources as $source) {
            $count = $source->getCount($query);
            if ($count > 0) {
                $totalCount += $count;
                foreach ($source->getEvents($query) as $event) {
                    $merged->insert($event);
                }
            }
        }

        if ($totalCount > 0) {
            $events = EventCollection::fromIterator(
                new LimitIterator($merged, 0, $query->getCount())
            );

            return new EventList($query, $events, $totalCount);
        }

        return EventList::createEmpty($query);
    }

    public function getGroupedEvents(EventQuery $query, string $groupBy = self::GROUP_BY_DAY): EventGroupList
    {
        $eventGroups = [];
        $interval = new DateInterval($groupBy);

        /** @var \DateTime $date */
        foreach ($query->getDateRange()->toDatePeriod($interval) as $date) {
            $innerQuery = $query
                ->modify()
                ->withDateRange(new DateRange($date, $date->add($interval)))
                ->getQuery();

            $eventGroups[] = EventGroup::fromEventList($this->getEvents($innerQuery));
        }

        return new EventGroupList($eventGroups);
    }
}

class_alias(CalendarService::class, 'EzSystems\EzPlatformCalendar\Calendar\CalendarService');
