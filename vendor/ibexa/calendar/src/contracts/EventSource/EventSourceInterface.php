<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Calendar\EventSource;

use Ibexa\Contracts\Calendar\EventQuery;

interface EventSourceInterface
{
    /**
     * Bulk-load Events with given id's.
     *
     * Impl. should skip not relevant and erroneous ids.
     *
     * @param string[] $eventIds
     *
     * @return \Ibexa\Contracts\Calendar\Event[]
     */
    public function loadEvents(array $eventIds): iterable;

    /**
     * Returns the number of events matching given $query.
     */
    public function getCount(EventQuery $query): int;

    /**
     * Returns events matching given $query.
     *
     * @return \Ibexa\Contracts\Calendar\Event[]
     */
    public function getEvents(EventQuery $query): iterable;
}

class_alias(EventSourceInterface::class, 'EzSystems\EzPlatformCalendar\Calendar\EventSource\EventSourceInterface');
