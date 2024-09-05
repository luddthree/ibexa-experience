<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Calendar;

/**
 * Defines parameters used to fetch events.
 *
 * Use {@link \Ibexa\Contracts\Calendar\EventQueryBuilder EventQueryBuilder} instead of creating this object directly.
 *
 * @see \Ibexa\Contracts\Calendar\EventQueryBuilder
 * @see \Ibexa\Contracts\Calendar\CalendarServiceInterface::getEvents()
 */
final class EventQuery
{
    public const DEFAULT_COUNT = 10;

    /** @var \Ibexa\Contracts\Calendar\DateRange */
    private $dateRange;

    /** @var int */
    private $count;

    /** @var \Ibexa\Contracts\Calendar\Cursor */
    private $cursor;

    /** @var string[]|null */
    private $types;

    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Language[]|null */
    private $languages;

    public function __construct(
        DateRange $dateRange,
        int $count = self::DEFAULT_COUNT,
        ?array $types = null,
        ?array $languages = null,
        ?Cursor $cursor = null
    ) {
        $this->dateRange = $dateRange;
        $this->count = $count;
        $this->types = $types;
        $this->languages = $languages;
        $this->cursor = $cursor;
    }

    /**
     * Returns date range to fetch events for.
     */
    public function getDateRange(): DateRange
    {
        return $this->dateRange;
    }

    /**
     * Returns number of events to fetch.
     */
    public function getCount(): int
    {
        return $this->count;
    }

    public function getCursor(): ?Cursor
    {
        return $this->cursor;
    }

    /**
     * Returns event types to fetch events for.
     *
     * @return string[]|null
     */
    public function getTypes(): ?array
    {
        return $this->types;
    }

    /**
     * Returns languages to fetch events for.
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Language[]|null
     */
    public function getLanguages(): ?array
    {
        return $this->languages;
    }

    /**
     * Allows to modify this query using EventQueryBuilder.
     *
     * @see \Ibexa\Contracts\Calendar\EventQueryBuilder
     */
    public function modify(): EventQueryBuilder
    {
        return EventQueryBuilder::fromQuery($this);
    }
}

class_alias(EventQuery::class, 'EzSystems\EzPlatformCalendar\Calendar\EventQuery');
