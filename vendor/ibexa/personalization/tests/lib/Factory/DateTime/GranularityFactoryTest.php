<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Personalization\Factory\DateTime;

use DateInterval;
use DateTime;
use DateTimeImmutable;
use Ibexa\Personalization\Factory\DateTime\GranularityFactory;
use Ibexa\Personalization\Factory\DateTime\GranularityFactoryInterface;
use Ibexa\Personalization\Value\GranularityDateTimeRange;
use Ibexa\Personalization\Value\TimePeriod;
use PHPUnit\Framework\TestCase;

final class GranularityFactoryTest extends TestCase
{
    private const GRANULARITY_1H = 'PT1H';
    private const GRANULARITY_1D = 'P1D';
    private const INTERVAL_0D = 'P0Y0M0DT0H';
    private const INTERVAL_6D = 'P0Y0M6DT0H';
    private const END_DATE_TIMESTAMP = 1597449600;

    /** @var \Ibexa\Personalization\Factory\DateTime\GranularityFactory */
    private $granularityFactory;

    /** @var \DateTimeImmutable */
    private $startDate;

    /** @var \DateTimeImmutable */
    private $endDate;

    public function setUp(): void
    {
        $this->granularityFactory = new GranularityFactory();
        $this->startDate = (new DateTimeImmutable())->setTime(00, 00, 00);
        $this->endDate = (new DateTimeImmutable())->setTime(00, 00, 00);
    }

    public function testCreateInstanceGranularityFactory(): void
    {
        self::assertInstanceOf(
            GranularityFactoryInterface::class,
            $this->granularityFactory
        );
    }

    public function testCreateForInterval24H(): void
    {
        $currentHour = (int)(new DateTime())->format('H');
        $startDate = (new DateTimeImmutable())->setTime($currentHour, 00, 00);
        $endDate = (new DateTimeImmutable())->setTime($currentHour, 00, 00);

        $granularityDateTimeRange = new GranularityDateTimeRange(
            self::GRANULARITY_1H,
            $startDate->sub(new DateInterval('PT24H')),
            $endDate
        );

        self::assertEquals(
            $granularityDateTimeRange,
            $this->granularityFactory->createFromInterval(TimePeriod::LAST_24_HOURS)
        );
    }

    public function testCreateForIntervalLast7Days(): void
    {
        $granularityDateTimeRange = new GranularityDateTimeRange(
            self::GRANULARITY_1D,
            $this->startDate->sub(new DateInterval('P7D')),
            $this->endDate
        );

        self::assertEquals(
            $granularityDateTimeRange,
            $this->granularityFactory->createFromInterval(TimePeriod::LAST_7_DAYS)
        );
    }

    public function testCreateForIntervalLast30Days(): void
    {
        $granularityDateTimeRange = new GranularityDateTimeRange(
            self::GRANULARITY_1D,
            $this->startDate->sub(new DateInterval('P30D')),
            $this->endDate
        );

        self::assertEquals(
            $granularityDateTimeRange,
            $this->granularityFactory->createFromInterval(TimePeriod::LAST_30_DAYS)
        );
    }

    public function testCreateForIntervalLastMonth(): void
    {
        $granularityDateTimeRange = new GranularityDateTimeRange(
            self::GRANULARITY_1D,
            $this->startDate->modify('first day of last month'),
            $this->endDate->modify('first day of this month')
        );

        self::assertEquals(
            $granularityDateTimeRange,
            $this->granularityFactory->createFromInterval(TimePeriod::LAST_MONTH)
        );
    }

    public function testReverseTransformForCustomRange(): void
    {
        $startDate = (new DateTimeImmutable('2020-08-09 00:00:0'));
        $endDate = (new DateTimeImmutable('2020-08-15 00:00:0'));

        $granularityDateTimeRange = new GranularityDateTimeRange(
            self::GRANULARITY_1D,
            $startDate,
            $endDate
        );

        self::assertEquals(
            $granularityDateTimeRange,
            $this->granularityFactory->createFromEndDateTimestampAndInterval(
                self::INTERVAL_6D,
                self::END_DATE_TIMESTAMP
            )
        );
    }

    public function testReverseTransformForCustomRange1Day(): void
    {
        $startDate = (new DateTimeImmutable('2020-08-15 00:00:0'));
        $endDate = (new DateTimeImmutable('2020-08-16 00:00:0'));

        $granularityDateTimeRange = new GranularityDateTimeRange(
            self::GRANULARITY_1H,
            $startDate,
            $endDate
        );

        self::assertEquals(
            $granularityDateTimeRange,
            $this->granularityFactory->createFromEndDateTimestampAndInterval(
                self::INTERVAL_0D,
                self::END_DATE_TIMESTAMP
            )
        );
    }
}

class_alias(GranularityFactoryTest::class, 'Ibexa\Platform\Tests\Personalization\Factory\DateTime\GranularityFactoryTest');
