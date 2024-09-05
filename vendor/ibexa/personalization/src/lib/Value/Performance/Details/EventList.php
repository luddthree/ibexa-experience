<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Value\Performance\Details;

use ArrayAccess;
use ArrayIterator;
use Ibexa\Contracts\Core\Exception\InvalidArgumentException;
use Ibexa\Contracts\Core\Repository\Exceptions\OutOfBoundsException;
use IteratorAggregate;
use JsonSerializable;
use Traversable;

/**
 * @implements IteratorAggregate<int, \Ibexa\Personalization\Value\Performance\Details\Event>
 * @implements ArrayAccess<int, \Ibexa\Personalization\Value\Performance\Details\Event>
 */
final class EventList implements IteratorAggregate, ArrayAccess, JsonSerializable
{
    /** @var array<\Ibexa\Personalization\Value\Performance\Details\Event> */
    private array $eventList;

    /**
     * @param array<\Ibexa\Personalization\Value\Performance\Details\Event> $eventList
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function __construct(array $eventList)
    {
        foreach ($eventList as $event) {
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

        $this->eventList = $eventList;
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->eventList);
    }

    public function offsetExists($offset): bool
    {
        return isset($this->eventList[$offset]);
    }

    public function offsetGet($offset): Event
    {
        if (false === $this->offsetExists($offset)) {
            throw new OutOfBoundsException(
                sprintf('The collection does not contain an element with index: %d', $offset)
            );
        }

        return $this->eventList[$offset];
    }

    public function offsetSet($offset, $value): void
    {
        throw new \BadMethodCallException(
            'Unsupported method'
        );
    }

    public function offsetUnset($offset): void
    {
        unset($this->eventList[$offset]);
    }

    /**
     * @return array<\Ibexa\Personalization\Value\Performance\Details\Event>
     */
    public function jsonSerialize(): array
    {
        return $this->eventList;
    }
}

class_alias(EventList::class, 'Ibexa\Platform\Personalization\Value\Performance\Details\EventList');
