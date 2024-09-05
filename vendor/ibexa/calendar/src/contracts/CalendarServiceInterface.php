<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Calendar;

use Ibexa\Contracts\Calendar\EventAction\EventActionContext;

interface CalendarServiceInterface
{
    public const GROUP_BY_DAY = 'P1D';
    public const GROUP_BY_WEEK = 'P7D';

    /**
     * Executes action.
     *
     * @throws \Ibexa\Calendar\Exception\UnsupportedActionException
     */
    public function executeAction(EventActionContext $context): void;

    /**
     * Bulk-load Events with given id's. Erroneous events will be skipped.
     *
     * @param string[] $eventsIds
     */
    public function loadEvents(array $eventsIds): EventCollection;

    /**
     * Get events matching given criteria.
     */
    public function getEvents(EventQuery $query): EventList;

    /**
     * Get and group events matching given criteria.
     */
    public function getGroupedEvents(EventQuery $query, string $groupBy = self::GROUP_BY_DAY): EventGroupList;
}

class_alias(CalendarServiceInterface::class, 'EzSystems\EzPlatformCalendar\Calendar\CalendarServiceInterface');
