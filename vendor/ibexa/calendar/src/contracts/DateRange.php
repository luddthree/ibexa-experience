<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Calendar;

use DateInterval;
use DatePeriod;
use DateTimeImmutable;
use DateTimeInterface;
use RangeException;

/**
 * Immutable date range representation where left bound is inclusive, right bound is exclusive.
 *
 * The following example range contains only one day:
 *
 * ```php
 * $range = new DateRange(new DateTimeImmutable('2020-01-01'), new DateTimeImmutable('2020-01-02'));
 * ```
 */
final class DateRange
{
    /** @var \DateTimeInterface */
    private $start;

    /** @var \DateTimeInterface */
    private $end;

    /**
     * @param \DateTimeInterface $start Left bound of the range (inclusive)
     * @param \DateTimeInterface $end Right bound of the range (exclusive)
     *
     * @throws \RangeException if $start > $end
     */
    public function __construct(DateTimeInterface $start, DateTimeInterface $end)
    {
        if (!($start instanceof DateTimeImmutable)) {
            $start = DateTimeImmutable::createFromFormat('U', (string)$start->getTimestamp(), $start->getTimezone());
        }

        if (!($end instanceof DateTimeImmutable)) {
            $end = DateTimeImmutable::createFromFormat('U', (string)$end->getTimestamp(), $end->getTimezone());
        }

        if ($start > $end) {
            throw new RangeException('Start date should be earlier or equal to the end date.');
        }

        $this->start = $start;
        $this->end = $end;
    }

    /**
     * Returns left bound of the range (inclusive).
     */
    public function getStartDate(): DateTimeInterface
    {
        return $this->start;
    }

    /**
     * Returns right bound of the range (exclusive).
     */
    public function getEndDate(): DateTimeInterface
    {
        return $this->end;
    }

    /**
     * Checks if given date is within the range.
     */
    public function contains(DateTimeInterface $dateTime): bool
    {
        return $dateTime >= $this->getStartDate() && $dateTime < $this->getEndDate();
    }

    /**
     * Converts date range (non-discrete value) to date period (discrete value) using given interval
     * (optional, P1D used as default value).
     *
     * Typical use case is to iterate over all the recurring the dates within range. For example:
     *
     * ```php
     * $period = $range->toDatePeriod(new DateInterval('P1W'));
     * foreach ($period as $date) {
     *     echo $date->format('Y-m-d')."\n";
     * }
     * ```
     *
     * @param \DateInterval|null $interval If not provided, then `DateInterval('P1D')` is default value.
     */
    public function toDatePeriod(?DateInterval $interval = null): DatePeriod
    {
        if ($interval === null) {
            $interval = new DateInterval('P1D');
        }

        return new DatePeriod($this->start, $interval, $this->end);
    }

    /**
     * Converts date range to string representation.
     *
     * Example output: [2024-01-01T00:00:00+00:00 - 2024-01-02T00:00:00+00:00].
     */
    public function __toString(): string
    {
        return sprintf(
            '[%s - %s]',
            $this->getStartDate()->format(DateTimeInterface::ISO8601),
            $this->getEndDate()->format(DateTimeInterface::ISO8601)
        );
    }
}

class_alias(DateRange::class, 'EzSystems\EzPlatformCalendar\Calendar\DateRange');
