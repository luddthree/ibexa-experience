<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Calendar;

use Iterator;
use IteratorAggregate;
use IteratorIterator;

/**
 * List of events being the result of executing event query.
 *
 * @see \Ibexa\Contracts\Calendar\CalendarServiceInterface::getEvents()
 */
class EventList implements IteratorAggregate
{
    /** @var \Ibexa\Contracts\Calendar\EventQuery */
    private $query;

    /** @var \Ibexa\Contracts\Calendar\EventCollection */
    private $events;

    /** @var int */
    private $totalCount;

    /**
     * @param \Ibexa\Contracts\Calendar\EventQuery $query Query used to fetch events.
     * @param \Ibexa\Contracts\Calendar\EventCollection $events List of events.
     * @param int $totalCount Total number of events matching query.
     */
    public function __construct(EventQuery $query, EventCollection $events, int $totalCount = 0)
    {
        $this->query = $query;
        $this->events = $events;
        $this->totalCount = $totalCount;
    }

    public function getEvents(): EventCollection
    {
        return $this->events;
    }

    /**
     * Returns total number of events matching query.
     */
    public function getTotalCount(): int
    {
        return $this->totalCount;
    }

    /**
     * Returns query used to fetch events.
     */
    public function getQuery(): EventQuery
    {
        return $this->query;
    }

    public function getIterator(): Iterator
    {
        return new IteratorIterator($this->events);
    }

    /**
     * Returns next page query or null if no more events.
     */
    public function getNextPageQuery(): ?EventQuery
    {
        if (!$this->events->isEmpty()) {
            $lastEvent = $this->events->last();

            $nextQuery = $this->query->modify();
            $nextQuery->withCursor(Cursor::fromEvent($lastEvent));
            $nextQuery->withDateRange(new DateRange(
                $lastEvent->getDateTime(),
                $this->query->getDateRange()->getEndDate()
            ));

            return $nextQuery->getQuery();
        }

        return null;
    }

    /**
     * Creates empty event list.
     */
    public static function createEmpty(EventQuery $query): self
    {
        return new self($query, new EventCollection());
    }
}

class_alias(EventList::class, 'EzSystems\EzPlatformCalendar\Calendar\EventList');
