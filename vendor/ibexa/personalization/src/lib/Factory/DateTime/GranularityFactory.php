<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Factory\DateTime;

use DateInterval;
use DateTime;
use Ibexa\Personalization\Value\GranularityDateTimeRange;
use Ibexa\Personalization\Value\TimePeriod;

final class GranularityFactory implements GranularityFactoryInterface
{
    private const GRANULARITY_1_HOUR = 'PT1H';
    private const GRANULARITY_1_DAY = 'P1D';
    private const INTERVAL_0_DAYS = 'P0Y0M0DT0H';

    public function createFromInterval(string $interval): GranularityDateTimeRange
    {
        return new GranularityDateTimeRange(
            $this->getGranularity($interval),
            $this->createStartDateTime($interval),
            $this->createEndDateTime($interval),
        );
    }

    public function createFromEndDateTimestampAndInterval(
        string $interval,
        int $endDateTimestamp
    ): GranularityDateTimeRange {
        return new GranularityDateTimeRange(
            $this->getGranularity($interval),
            $this->createStartDateTime($interval, $endDateTimestamp),
            $this->createEndDateTime($interval, $endDateTimestamp),
        );
    }

    public function createStartDateTime(string $interval, ?int $timestamp = null): DateTime
    {
        $startDate = new DateTime();

        if ($timestamp > 0) {
            $startDate->setTimestamp($timestamp);
        }
        if ($interval === TimePeriod::LAST_MONTH) {
            $startDate->modify('first day of last month');
        } else {
            $startDate->sub(
                new DateInterval($interval)
            );
        }

        if ($interval === TimePeriod::LAST_24_HOURS) {
            return $this->getDateWithCurrentTime($startDate);
        }

        $startDate->setTime(00, 00, 00);

        return $startDate;
    }

    public function createEndDateTime(string $interval, ?int $timestamp = null): DateTime
    {
        $endDate = new DateTime();

        if ($interval === TimePeriod::LAST_24_HOURS) {
            return $this->getDateWithCurrentTime($endDate);
        }
        if ($interval === TimePeriod::LAST_MONTH) {
            $endDate->modify('first day of this month');
        }
        if ($interval !== TimePeriod::LAST_MONTH && $timestamp > 0) {
            $endDate->setTimestamp($timestamp);
        }
        if ($interval === self::INTERVAL_0_DAYS) {
            $endDate->add(
                new DateInterval(TimePeriod::LAST_24_HOURS)
            );
        }

        return $endDate->setTime(00, 00, 00);
    }

    public function getGranularity(string $interval): string
    {
        if (
            $interval === TimePeriod::LAST_24_HOURS
            || $interval === self::INTERVAL_0_DAYS
        ) {
            return self::GRANULARITY_1_HOUR;
        }

        return self::GRANULARITY_1_DAY;
    }

    private function getDateWithCurrentTime(DateTime $dateTime): DateTime
    {
        $currentHour = (new DateTime())->format('H');

        return $dateTime->setTime((int)$currentHour, 00, 00);
    }
}

class_alias(GranularityFactory::class, 'Ibexa\Platform\Personalization\Factory\DateTime\GranularityFactory');
