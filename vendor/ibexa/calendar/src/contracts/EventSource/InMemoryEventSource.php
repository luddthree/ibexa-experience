<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Calendar\EventSource;

use Closure;
use Ibexa\Contracts\Calendar\Cursor;
use Ibexa\Contracts\Calendar\Event;
use Ibexa\Contracts\Calendar\EventCollection;
use Ibexa\Contracts\Calendar\EventQuery;
use Ibexa\Contracts\Calendar\LanguageBasedEvent;

/**
 * @final
 */
class InMemoryEventSource implements EventSourceInterface
{
    private EventCollection $events;

    public function __construct(EventCollection $events)
    {
        $this->events = $events;
    }

    public function getCount(EventQuery $query): int
    {
        return $this->events->filter($this->getPredicateFromQuery($query))->count();
    }

    public function loadEvents(array $eventIds): iterable
    {
        $predicate = static function (Event $event) use ($eventIds): bool {
            return in_array($event->getId(), $eventIds);
        };

        return $this->events->filter($predicate)->getIterator();
    }

    public function getEvents(EventQuery $query): iterable
    {
        return $this->events
            ->filter($this->getPredicateFromQuery($query))
            ->slice(0, $query->getCount());
    }

    private function getPredicateFromQuery(EventQuery $query): Closure
    {
        $offset = -1;
        if ($query->getCursor() !== null) {
            $offset = $this->getOffsetFromCursor($query->getCursor());
        }

        $predicate = static function (Event $event, $index) use ($query, $offset): bool {
            if ($index <= $offset) {
                return false;
            }

            if (!$query->getDateRange()->contains($event->getDateTime())) {
                return false;
            }

            if (!empty($query->getTypes()) && !in_array($event->getType()->getTypeIdentifier(), $query->getTypes())) {
                return false;
            }

            if (!empty($query->getLanguages())) {
                if (!($event instanceof LanguageBasedEvent) || !in_array($event->getLanguage(), $query->getLanguages())) {
                    return false;
                }
            }

            return true;
        };

        return $predicate;
    }

    private function getOffsetFromCursor(Cursor $cursor): int
    {
        $predicate = static function (Event $event) use ($cursor): bool {
            return $event->getType()->getTypeIdentifier() === $cursor->getEventType()
                && $event->getId() === $cursor->getEventId();
        };

        return (int)$this->events->find($predicate);
    }
}

class_alias(InMemoryEventSource::class, 'EzSystems\EzPlatformCalendar\Calendar\EventSource\InMemoryEventSource');
