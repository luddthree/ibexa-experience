<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Value\Performance\Summary;

use ArrayAccess;
use ArrayIterator;
use BadMethodCallException;
use Ibexa\Contracts\Core\Exception\InvalidArgumentException;
use IteratorAggregate;
use JsonSerializable;
use Traversable;

/**
 * @implements IteratorAggregate<int, \Ibexa\Personalization\Value\Performance\Summary\Event>
 * @implements ArrayAccess<int, \Ibexa\Personalization\Value\Performance\Summary\Event>
 */
final class EventList implements IteratorAggregate, ArrayAccess, JsonSerializable
{
    /** @var array<\Ibexa\Personalization\Value\Performance\Summary\Event> */
    private array $events;

    /**
     * @param array<\Ibexa\Personalization\Value\Performance\Summary\Event> $events
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function __construct(array $events)
    {
        foreach ($events as $event) {
            if (!$event instanceof Event) {
                /** @var mixed $event */
                throw new InvalidArgumentException(
                    '$event',
                    sprintf(
                        'Must be of type: %s, %s given',
                        Event::class,
                        is_object($event) ? get_class($event) : gettype($event)
                    )
                );
            }
        }

        $this->events = $events;
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->events);
    }

    public function offsetExists($offset): bool
    {
        return isset($this->events[$offset]);
    }

    public function offsetGet($offset): Event
    {
        return $this->events[$offset];
    }

    public function offsetSet($offset, $value): void
    {
        throw new BadMethodCallException(
            'Unsupported method'
        );
    }

    public function offsetUnset($offset): void
    {
        unset($this->events[$offset]);
    }

    /**
     * @return array<\Ibexa\Personalization\Value\Performance\Summary\Event>
     */
    public function jsonSerialize(): array
    {
        return $this->events;
    }

    public function findById(string $id): ?Event
    {
        foreach ($this->events as $event) {
            if ($id === $event->getName()) {
                return $event;
            }
        }

        return null;
    }
}

class_alias(EventList::class, 'Ibexa\Platform\Personalization\Value\Performance\Summary\EventList');
