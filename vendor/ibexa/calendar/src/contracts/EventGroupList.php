<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Calendar;

use ArrayIterator;
use Countable;
use Iterator;
use IteratorAggregate;

/**
 * List of grouped events.
 */
final class EventGroupList implements Countable, IteratorAggregate
{
    /** @var \Ibexa\Contracts\Calendar\EventGroup[] */
    private $groups;

    /**
     * @param \Ibexa\Contracts\Calendar\EventGroup[] $groups
     */
    public function __construct(iterable $groups)
    {
        $this->groups = $groups;
    }

    /**
     * @return \Ibexa\Contracts\Calendar\EventGroup[]
     */
    public function getGroups(): iterable
    {
        return $this->groups;
    }

    public function getIterator(): Iterator
    {
        return new ArrayIterator($this->groups);
    }

    public function count(): int
    {
        return count($this->groups);
    }

    public static function createEmpty(): self
    {
        return new self([]);
    }
}

class_alias(EventGroupList::class, 'EzSystems\EzPlatformCalendar\Calendar\EventGroupList');
