<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Calendar\Calendar;

use DateInterval;
use DatePeriod;
use DateTime;
use Ibexa\Contracts\Calendar\DateRange;
use PHPUnit\Framework\TestCase;
use RangeException;

class DateRangeTest extends TestCase
{
    public function testConstruct(): void
    {
        $a = new DateTime('1970-01-01 00:00:00');
        $b = new DateTime('2038-01-19 00:00:00');

        $range = new DateRange($a, $b);

        $this->assertEquals($a, $range->getStartDate());
        $this->assertEquals($b, $range->getEndDate());
    }

    public function testConstructThrowsRangeException(): void
    {
        $a = new DateTime('2038-01-19 00:00:00');
        $b = new DateTime('1970-01-01 00:00:00');

        $this->expectException(RangeException::class);
        $this->expectExceptionMessage('Start date should be earlier or equal to the end date.');

        $range = new DateRange($a, $b);
    }

    public function testContains(): void
    {
        $a = new DateTime('1970-01-01 00:00:00');
        $b = new DateTime('2038-01-19 00:00:00');
        $x = new DateTime('2000-01-01 00:00:00');
        $y = new DateTime('2039-01-01 00:00:00');

        $range = new DateRange($a, $b);

        $this->assertTrue($range->contains($a));
        $this->assertTrue($range->contains($x));
        $this->assertFalse($range->contains($b));
        $this->assertFalse($range->contains($y));
    }

    public function testToDatePeriod(): void
    {
        $a = new DateTime('1970-01-01 00:00:00');
        $b = new DateTime('2038-01-19 00:00:00');
        $interval = new DateInterval('P1D');

        $range = new DateRange($a, $b);

        $this->assertEquals(
            new DatePeriod($a, $interval, $b),
            $range->toDatePeriod($interval)
        );
    }

    public function testToString(): void
    {
        $a = new DateTime('1970-01-01 00:00:00');
        $b = new DateTime('2038-01-19 00:00:00');

        $range = new DateRange($a, $b);

        $this->assertEquals('[1970-01-01T00:00:00+0000 - 2038-01-19T00:00:00+0000]', (string)$range);
    }
}

class_alias(DateRangeTest::class, 'EzSystems\EzPlatformCalendar\Tests\Calendar\DateRangeTest');
