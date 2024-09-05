<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Calendar;

use LogicException;

/**
 * Allows to build event query in a fluent way.
 *
 * The following example query is searching for events
 * occurring in 2020,
 * and typed 'scheduled_publication':
 *
 * ```php
 * $query = (new EventQueryBuilder())
 *  ->withDateRange(new DateRange(new DateTime('2020-01-01'), new DateTime('2021-01-01')))
 *  ->withTypes(['scheduled_publication'])
 *  ->getQuery();
 * ```
 */
final class EventQueryBuilder
{
    /** @var \Ibexa\Contracts\Calendar\DateRange|null */
    private $dateRange;

    /** @var int */
    private $count;

    /** @var \Ibexa\Contracts\Calendar\Cursor|null */
    private $cursor;

    /** @var string[]|null */
    private $types;

    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Language[]|null */
    private $languages;

    public function __construct()
    {
        $this->count = EventQuery::DEFAULT_COUNT;
    }

    public function withCount(int $count): self
    {
        $this->count = $count;

        return $this;
    }

    public function withDateRange(DateRange $dateRange): self
    {
        $this->dateRange = $dateRange;

        return $this;
    }

    public function withCursor(?Cursor $cursor): self
    {
        $this->cursor = $cursor;

        return $this;
    }

    /**
     * @param string[]|null $types
     */
    public function withTypes(?array $types): self
    {
        $this->types = $types;

        return $this;
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Language[]|null $languages
     */
    public function withLanguages(?array $languages): self
    {
        $this->languages = $languages;

        return $this;
    }

    /**
     * Builds the query.
     *
     * @throws \LogicException if date range is not specified
     */
    public function getQuery(): EventQuery
    {
        if ($this->dateRange === null) {
            throw new LogicException('Date range is required');
        }

        return new EventQuery(
            $this->dateRange,
            $this->count,
            $this->types,
            $this->languages,
            $this->cursor
        );
    }

    public static function fromQuery(EventQuery $query): self
    {
        $builder = new self();
        $builder->dateRange = $query->getDateRange();
        $builder->count = $query->getCount();
        $builder->types = $query->getTypes();
        $builder->languages = $query->getLanguages();
        $builder->cursor = $query->getCursor();

        return $builder;
    }
}

class_alias(EventQueryBuilder::class, 'EzSystems\EzPlatformCalendar\Calendar\EventQueryBuilder');
