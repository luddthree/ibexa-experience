<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Scheduler\Calendar;

use ArrayIterator;
use Ibexa\Contracts\Calendar\Cursor;
use Ibexa\Contracts\Calendar\EventQuery;
use Ibexa\Contracts\Calendar\EventSource\EventSourceInterface;
use Ibexa\Contracts\Calendar\EventType\EventTypeInterface;
use Ibexa\Contracts\Scheduler\Repository\DateBasedEntriesListInterface;
use Ibexa\Contracts\Scheduler\ValueObject\ScheduledEntry;

abstract class AbstractDateBasedActionEventSource implements EventSourceInterface
{
    /** @var \Ibexa\Contracts\Scheduler\Repository\DateBasedEntriesListInterface */
    private $dateBasedService;

    /** @var \Ibexa\Contracts\Calendar\EventType\EventTypeInterface */
    private $eventType;

    /** @var \Ibexa\Scheduler\Calendar\PrefixDecorator */
    private $prefixDecorator;

    public function __construct(
        DateBasedEntriesListInterface $dateBasedService,
        EventTypeInterface $eventType
    ) {
        $this->dateBasedService = $dateBasedService;
        $this->eventType = $eventType;
        $this->prefixDecorator = new PrefixDecorator($this->eventType->getTypeIdentifier());
    }

    public function loadEvents(array $eventIds): iterable
    {
        $scheduledEntriesIds = [];
        foreach ($eventIds as $eventId) {
            if ($this->prefixDecorator->isDecorated($eventId)) {
                $scheduledEntriesIds[] = $this->prefixDecorator->undecorate($eventId);
            }
        }

        return new ArrayIterator($this->buildEventDomainObjectList(
            $this->dateBasedService->getScheduledEntriesByIds($scheduledEntriesIds)
        ));
    }

    public function getCount(EventQuery $query): int
    {
        if ($this->isEventTypeExcluded($query)) {
            return 0;
        }

        return $this->dateBasedService->countScheduledEntriesInDateRange(
            $query->getDateRange()->getStartDate(),
            $query->getDateRange()->getEndDate(),
            $query->getLanguages() ?? [],
            $this->getScheduledEntryAtCursor($query->getCursor())
        );
    }

    public function getEvents(EventQuery $query): iterable
    {
        if ($this->isEventTypeExcluded($query)) {
            return [];
        }

        $scheduledVersions = $this->dateBasedService->getScheduledEntriesInDateRange(
            $query->getDateRange()->getStartDate(),
            $query->getDateRange()->getEndDate(),
            $query->getLanguages() ?? [],
            $this->getScheduledEntryAtCursor($query->getCursor()),
            $query->getCount()
        );

        return $this->buildEventDomainObjectList($scheduledVersions);
    }

    /**
     * Builds a Future Publication Event object.
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    abstract protected function buildEventDomainObject(
        EventTypeInterface $eventType,
        string $id,
        ScheduledEntry $scheduledscheduledEntry
    ): ScheduledEntryBasedEvent;

    /**
     * Returns true if $this->eventType->getTypeIdentifier() events are excluded from query.
     */
    private function isEventTypeExcluded(EventQuery $query): bool
    {
        return $query->getTypes() !== null && !in_array($this->eventType->getTypeIdentifier(), $query->getTypes());
    }

    /**
     * Returns id of scheduled entry pointed by cursor.
     */
    private function getScheduledEntryAtCursor(?Cursor $cursor): ?int
    {
        $typeIdentifier = $this->eventType->getTypeIdentifier();

        if ($cursor !== null && $cursor->getEventType() === $typeIdentifier) {
            return (int)$this->prefixDecorator->undecorate($cursor->getEventId());
        }

        return null;
    }

    /**
     * Builds a list of Event objects.
     *
     * @param \Ibexa\Contracts\Scheduler\ValueObject\ScheduledEntry[] $scheduledEntries
     *
     * @return \Ibexa\Scheduler\Calendar\ScheduledEntryBasedEvent[]
     */
    private function buildEventDomainObjectList(array $scheduledEntries): iterable
    {
        return array_map(function (ScheduledEntry $scheduledEntry) {
            return $this->buildEventDomainObject(
                $this->eventType,
                $this->prefixDecorator->decorate((string)$scheduledEntry->id),
                $scheduledEntry
            );
        }, $scheduledEntries);
    }
}

class_alias(AbstractDateBasedActionEventSource::class, 'EzSystems\DateBasedPublisher\Core\Calendar\AbstractDateBasedActionEventSource');
