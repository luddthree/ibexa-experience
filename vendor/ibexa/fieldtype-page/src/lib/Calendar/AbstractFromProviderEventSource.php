<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\Calendar;

use Ibexa\Contracts\Calendar\Event;
use Ibexa\Contracts\Calendar\EventQuery;
use Ibexa\Contracts\Calendar\EventSource\EventSourceInterface;
use Ibexa\Contracts\Calendar\EventType\EventTypeInterface;
use Ibexa\FieldTypePage\Calendar\Provider\EntriesProviderInterface;

abstract class AbstractFromProviderEventSource implements EventSourceInterface
{
    /** @var \Ibexa\FieldTypePage\Calendar\Provider\EntriesProviderInterface */
    private $entriesProvider;

    /** @var \Ibexa\Contracts\Calendar\EventType\EventTypeInterface */
    private $eventType;

    /**
     * @var \Ibexa\FieldTypePage\Calendar\ScheduledEntryIdProvider
     */
    private $scheduledEntryIdProvider;

    /** @var \Ibexa\FieldTypePage\Calendar\PrefixDecorator */
    protected $prefixDecorator;

    public function __construct(
        EntriesProviderInterface $entriesProvider,
        EventTypeInterface $eventType,
        ScheduledEntryIdProvider $scheduledEntryIdProvider
    ) {
        $this->entriesProvider = $entriesProvider;
        $this->eventType = $eventType;
        $this->scheduledEntryIdProvider = $scheduledEntryIdProvider;
        $this->prefixDecorator = new PrefixDecorator($this->eventType->getTypeIdentifier());
    }

    /**
     * @return iterable<\Ibexa\Contracts\Calendar\Event>
     */
    public function loadEvents(array $eventIds): iterable
    {
        $scheduledEntriesIds = [];
        foreach ($eventIds as $eventId) {
            if ($this->prefixDecorator->isDecorated($eventId)) {
                $scheduledEntriesIds[] = $this->prefixDecorator->undecorate($eventId);
            }
        }

        return $this->buildEventList(
            $this->entriesProvider->getScheduledEntriesByIds($scheduledEntriesIds)
        );
    }

    public function getCount(EventQuery $query): int
    {
        if ($this->isEventTypeExcluded($query)) {
            return 0;
        }

        return $this->entriesProvider->countScheduledEntriesInDateRange(
            $query->getDateRange()->getStartDate(),
            $query->getDateRange()->getEndDate(),
            $query->getLanguages() ?? [],
            $this->scheduledEntryIdProvider->fromCursor($query->getCursor(), $this->eventType->getTypeIdentifier())
        );
    }

    /**
     * @return \Ibexa\Contracts\Calendar\Event[]
     */
    public function getEvents(EventQuery $query): iterable
    {
        if ($this->isEventTypeExcluded($query)) {
            return [];
        }

        $scheduledVersions = $this->entriesProvider->getScheduledEntriesInDateRange(
            $query->getDateRange()->getStartDate(),
            $query->getDateRange()->getEndDate(),
            $query->getLanguages() ?? [],
            $this->scheduledEntryIdProvider->fromCursor($query->getCursor(), $this->eventType->getTypeIdentifier()),
            $query->getCount()
        );

        return $this->buildEventList($scheduledVersions);
    }

    abstract protected function buildEvent(
        EventTypeInterface $eventType,
        ScheduledEntryInterface $scheduledEntry,
        string $eventId
    ): Event;

    private function isEventTypeExcluded(EventQuery $query): bool
    {
        return $query->getTypes() !== null && !in_array($this->eventType->getTypeIdentifier(), $query->getTypes());
    }

    /**
     * @param ScheduledEntryInterface[] $scheduledEntries
     *
     * @return \Ibexa\Contracts\Calendar\Event[]
     */
    private function buildEventList(array $scheduledEntries): iterable
    {
        return array_map(function (ScheduledEntryInterface $entry): Event {
            return $this->buildEvent(
                $this->eventType,
                $entry,
                $this->prefixDecorator->decorate($entry->getId())
            );
        }, $scheduledEntries);
    }
}

class_alias(AbstractFromProviderEventSource::class, 'EzSystems\EzPlatformPageFieldType\Calendar\AbstractFromProviderEventSource');
