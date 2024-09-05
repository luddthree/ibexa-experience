<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Calendar;

use ArrayIterator;
use Closure;
use Countable;
use Iterator;
use IteratorAggregate;
use Traversable;

/**
 * Collection of SORTED events by timestamp.
 */
final class EventCollection implements IteratorAggregate, Countable
{
    /** @var \Ibexa\Contracts\Calendar\Event[] */
    private $events;

    /**
     * @param \Ibexa\Contracts\Calendar\Event[] $events
     */
    public function __construct(array $events = [])
    {
        $this->events = array_values($events);
    }

    public function isEmpty(): bool
    {
        return empty($this->events);
    }

    /**
     * Returns first event in collection (or null if empty).
     */
    public function first(): ?Event
    {
        return !empty($this->events) ? reset($this->events) : null;
    }

    /**
     * Returns last event in collection (or null if empty).
     */
    public function last(): ?Event
    {
        return !empty($this->events) ? end($this->events) : null;
    }

    /**
     * Returns index of first event matching given predicate or null if not found.
     *
     * The following example gets either the first event having an ID of 123, or null:
     *
     * ```php
     * $idx = $events->find(fn(Event $e): bool => $e->getId() === '123');
     * ```
     */
    public function find(Closure $predicate): ?int
    {
        foreach ($this->events as $idx => $event) {
            if ($predicate($event) === true) {
                return $idx;
            }
        }

        return null;
    }

    /**
     * Returns collection of events matching given predicate.
     *
     * The following example gets a collection of future events:
     *
     * ```php
     * $filtered = $events->filter(fn(Event $e): bool => $e->getDateTime() > new DateTimeImmutable('now'));
     * ```
     */
    public function filter(Closure $predicate): self
    {
        return new self(array_filter($this->events, $predicate, ARRAY_FILTER_USE_BOTH));
    }

    /**
     * Maps collection of events using given callback.
     *
     * The following example gets an array of event names:
     *
     * ```php
     * $names = $events->map(fn(Event $e): string => $e->getName());
     * ```
     */
    public function map(Closure $callback): iterable
    {
        return array_map($callback, $this->events);
    }

    /**
     * Slices collection of events.
     *
     * @param int $offset Start offset
     * @param int|null $length Length of slice. If $length is null, then slice to the end of the collection.
     */
    public function slice(int $offset, ?int $length = null): self
    {
        return new self(array_slice($this->events, $offset, $length, true));
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->events);
    }

    public function count(): int
    {
        return count($this->events);
    }

    /**
     * Creates events collection from given iterator.
     */
    public static function fromIterator(Iterator $iterator): self
    {
        return new self(iterator_to_array($iterator));
    }

    /**
     * Creates events collection from given events.
     *
     * Usage example: `$events = EventCollection::of($event1, $event2, $event3);`
     */
    public static function of(Event ...$events): self
    {
        return new self($events);
    }
}

class_alias(EventCollection::class, 'EzSystems\EzPlatformCalendar\Calendar\EventCollection');
