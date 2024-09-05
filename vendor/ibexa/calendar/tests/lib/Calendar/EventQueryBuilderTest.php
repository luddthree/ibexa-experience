<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Calendar\Calendar;

use DateTime;
use Ibexa\Contracts\Calendar\Cursor;
use Ibexa\Contracts\Calendar\DateRange;
use Ibexa\Contracts\Calendar\EventQuery;
use Ibexa\Contracts\Calendar\EventQueryBuilder;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use PHPUnit\Framework\TestCase;

class EventQueryBuilderTest extends TestCase
{
    private const CURSOR_DATA = 'MTU1NjY2ODgwMDp0ZXN0OjAwODVlOTQ4LTg2ZDEtMTFlOS1iYzQyLTUyNmFmNzc2NGY2NA==';

    public function testGetQueryWithDateRange(): void
    {
        $expected = new EventQuery(
            new DateRange(
                new DateTime('1970-01-01 00:00:00'),
                new DateTime('2038-01-31 00:00:00')
            )
        );

        $builder = new EventQueryBuilder();
        $builder->withDateRange(new DateRange(
            new DateTime('1970-01-01 00:00:00'),
            new DateTime('2038-01-31 00:00:00')
        ));

        $this->assertEquals($expected, $builder->getQuery());
    }

    /**
     * @depends testGetQueryWithDateRange
     */
    public function testGetQueryWithCount(): void
    {
        $expected = new EventQuery(
            new DateRange(
                new DateTime('1970-01-01 00:00:00'),
                new DateTime('2038-01-31 00:00:00')
            ),
            20
        );

        $builder = new EventQueryBuilder();
        $builder->withDateRange(new DateRange(
            new DateTime('1970-01-01 00:00:00'),
            new DateTime('2038-01-31 00:00:00')
        ));
        $builder->withCount(20);

        $this->assertEquals($expected, $builder->getQuery());
    }

    /**
     * @depends testGetQueryWithDateRange
     */
    public function testGetQueryWithTypes(): void
    {
        $expected = new EventQuery(
            new DateRange(
                new DateTime('1970-01-01 00:00:00'),
                new DateTime('2038-01-31 00:00:00')
            ),
            EventQuery::DEFAULT_COUNT,
            ['foo', 'bar', 'baz']
        );

        $builder = new EventQueryBuilder();
        $builder->withDateRange(new DateRange(
            new DateTime('1970-01-01 00:00:00'),
            new DateTime('2038-01-31 00:00:00')
        ));
        $builder->withTypes(['foo', 'bar', 'baz']);

        $this->assertEquals($expected, $builder->getQuery());
    }

    /**
     * @depends testGetQueryWithDateRange
     */
    public function testGetQueryWithCursor(): void
    {
        $cursor = Cursor::fromString(self::CURSOR_DATA);

        $expected = new EventQuery(
            new DateRange(
                new DateTime('1970-01-01 00:00:00'),
                new DateTime('2038-01-31 00:00:00')
            ),
            EventQuery::DEFAULT_COUNT,
            null,
            null,
            $cursor
        );

        $builder = new EventQueryBuilder();
        $builder->withDateRange(new DateRange(
            new DateTime('1970-01-01 00:00:00'),
            new DateTime('2038-01-31 00:00:00')
        ));
        $builder->withCursor($cursor);

        $this->assertEquals($expected, $builder->getQuery());
    }

    /**
     * @depends testGetQueryWithDateRange
     */
    public function testGetQueryWithLanguages(): void
    {
        $languages = [
            new Language(['languageCode' => 'eng-GB']),
            new Language(['languageCode' => 'ger-DE']),
            new Language(['languageCode' => 'pol-PL']),
        ];

        $expected = new EventQuery(
            new DateRange(
                new DateTime('1970-01-01 00:00:00'),
                new DateTime('2038-01-31 00:00:00')
            ),
            EventQuery::DEFAULT_COUNT,
            null,
            $languages
        );

        $builder = new EventQueryBuilder();
        $builder->withDateRange(new DateRange(
            new DateTime('1970-01-01 00:00:00'),
            new DateTime('2038-01-31 00:00:00')
        ));
        $builder->withLanguages($languages);

        $this->assertEquals($expected, $builder->getQuery());
    }

    public function testFromQuery(): void
    {
        $cursor = Cursor::fromString(self::CURSOR_DATA);

        $expected = new EventQuery(
            new DateRange(
                new DateTime('1970-01-01 00:00:00'),
                new DateTime('2038-01-31 00:00:00')
            ),
            EventQuery::DEFAULT_COUNT,
            ['foo', 'bar', 'baz'],
            [
                new Language(['languageCode' => 'eng-GB']),
                new Language(['languageCode' => 'ger-DE']),
                new Language(['languageCode' => 'fre-FR']),
            ],
            $cursor
        );

        $actual = EventQueryBuilder::fromQuery($expected)->getQuery();

        $this->assertEquals($expected, $actual);
    }
}

class_alias(EventQueryBuilderTest::class, 'EzSystems\EzPlatformCalendar\Tests\Calendar\EventQueryBuilderTest');
