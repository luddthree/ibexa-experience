<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Calendar\Calendar\EventSource;

use DateTime;
use Ibexa\Contracts\Calendar\Cursor;
use Ibexa\Contracts\Calendar\DateRange;
use Ibexa\Contracts\Calendar\Event;
use Ibexa\Contracts\Calendar\EventQuery;
use Ibexa\Contracts\Calendar\EventQueryBuilder;
use Ibexa\Contracts\Calendar\EventSource\EventSourceInterface;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Tests\Calendar\Calendar\AbstractCalendarTestCase;
use Ibexa\Tests\Calendar\Calendar\Stubs\LanguageBasedTestEvent;
use Ibexa\Tests\Calendar\Calendar\Stubs\TestEvent;
use Ibexa\Tests\Calendar\Calendar\Stubs\TestEventType;

abstract class AbstractEventSourceTestCase extends AbstractCalendarTestCase
{
    /**
     * @dataProvider dataProviderForLoadEventsTest
     */
    public function testLoadEvents(array $availableEvents, array $loadedEventsIds, array $expectedEvents): void
    {
        $source = $this->createEventsSource($availableEvents);

        $this->assertEvents($expectedEvents, $source->loadEvents($loadedEventsIds));
    }

    public function testEmptySource(): void
    {
        $emptySource = $this->createEventsSource([]);

        $query = new EventQuery(new DateRange(
            new DateTime('2000-01-01 00:00:00'),
            new DateTime('2000-01-31 23:59:59')
        ));

        $this->assertEmptyResultsForQuery($emptySource, $query);
    }

    /**
     * @dataProvider dataProviderForDateRangeCriteriaTest
     */
    public function testDateRangeCriteria(array $events, EventQuery $query, int $expectedCount, array $expectedEvents): void
    {
        $this->doTestCriteria($events, $query, $expectedCount, $expectedEvents);
    }

    /**
     * @dataProvider dataProviderForTypesCriteriaTest
     */
    public function testTypesCriteria(array $events, EventQuery $query, int $expectedCount, array $expectedEvents): void
    {
        $this->doTestCriteria($events, $query, $expectedCount, $expectedEvents);
    }

    /**
     * @dataProvider dataProviderForLanguageCriteriaTest
     */
    public function testLanguageCriteria(array $events, EventQuery $query, int $expectedCount, array $expectedEvents): void
    {
        $this->doTestCriteria($events, $query, $expectedCount, $expectedEvents);
    }

    /**
     * @dataProvider dataProviderForPaginationTest
     */
    public function testPagination(array $events, EventQuery $query, int $expectedCount, array $expectedEvents): void
    {
        $this->doTestCriteria($events, $query, $expectedCount, $expectedEvents);
    }

    public function dataProviderForLoadEventsTest(): array
    {
        $events = $this->createEvents([
            'A' => '2000-01-01',
            'B' => '2000-01-02',
            'C' => '2000-01-03',
            'D' => '2000-01-04',
            'E' => '2000-01-05',
            'F' => '2000-01-06',
        ]);

        return [
            [
                [],
                [],
                [],
            ],
            [
                $events,
                ['X', 'Y', 'Z'],
                [],
            ],
            [
                $events,
                ['A', 'B', 'C'],
                ['A', 'B', 'C'],
            ],
            [
                $events,
                ['A', 'B', 'C', 'X', 'Y', 'Z'],
                ['A', 'B', 'C'],
            ],
        ];
    }

    public function dataProviderForLanguageCriteriaTest(): array
    {
        $type = new TestEventType('test');

        $en = new Language(['languageCode' => 'eng-GB']);
        $fr = new Language(['languageCode' => 'fre-FR']);
        $de = new Language(['languageCode' => 'ger-DE']);
        $pl = new Language(['languageCode' => 'pol-PL']);

        $events = [
            'A' => new LanguageBasedTestEvent($type, 'A', new DateTime('2000-01-01 00:00:00'), $en),
            'B' => new LanguageBasedTestEvent($type, 'B', new DateTime('2000-01-02 00:00:00'), $en),
            'C' => new LanguageBasedTestEvent($type, 'C', new DateTime('2000-01-03 00:00:00'), $fr),
            'D' => new LanguageBasedTestEvent($type, 'D', new DateTime('2000-01-04 00:00:00'), $de),
            'E' => new LanguageBasedTestEvent($type, 'E', new DateTime('2000-01-05 00:00:00'), $fr),
            'F' => new LanguageBasedTestEvent($type, 'F', new DateTime('2000-01-06 00:00:00'), $en),
        ];

        $queryBuilder = new EventQueryBuilder();
        $queryBuilder->withCount(3);
        $queryBuilder->withDateRange(new DateRange(
            new DateTime('2000-01-01 00:00:00'),
            new DateTime('2000-01-31 23:59:59')
        ));

        return [
            [
                $events,
                $queryBuilder->withLanguages([$pl])->getQuery(),
                0,
                [],
            ],
            [
                $events,
                $queryBuilder->withLanguages([$fr])->getQuery(),
                2,
                ['C', 'E'],
            ],
            [
                $events,
                $queryBuilder->withLanguages([$en, $fr])->getQuery(),
                5,
                ['A', 'B', 'C'],
            ],
        ];
    }

    public function dataProviderForTypesCriteriaTest(): array
    {
        $foo = new TestEventType('foo');
        $bar = new TestEventType('bar');
        $baz = new TestEventType('baz');

        $events = [
            'A' => new TestEvent($foo, 'A', new DateTime('2000-01-01 00:00:00')),
            'B' => new TestEvent($bar, 'B', new DateTime('2000-01-02 00:00:00')),
            'C' => new TestEvent($baz, 'C', new DateTime('2000-01-03 00:00:00')),
            'D' => new TestEvent($bar, 'D', new DateTime('2000-01-04 00:00:00')),
            'E' => new TestEvent($foo, 'E', new DateTime('2000-01-05 00:00:00')),
            'F' => new TestEvent($foo, 'F', new DateTime('2000-01-06 00:00:00')),
        ];

        $queryBuilder = new EventQueryBuilder();
        $queryBuilder->withCount(3);
        $queryBuilder->withDateRange(new DateRange(
            new DateTime('2000-01-01 00:00:00'),
            new DateTime('2000-01-31 23:59:59')
        ));

        return [
            [
                $events,
                $queryBuilder->withTypes(['foobaz'])->getQuery(),
                0,
                [],
            ],
            [
                $events,
                $queryBuilder->withTypes(['baz'])->getQuery(),
                1,
                ['C'],
            ],
            [
                $events,
                $queryBuilder->withTypes(['foo', 'baz'])->getQuery(),
                4,
                ['A', 'C', 'E'],
            ],
        ];
    }

    public function dataProviderForPaginationTest(): array
    {
        $events = $this->createEvents([
            'A' => '2000-01-01',
            'B' => '2000-01-02',
            'C' => '2000-01-03',
            'D' => '2000-01-04',
            'E' => '2000-01-05',
            'F' => '2000-01-06',
        ]);

        $queryBuilder = new EventQueryBuilder();
        $queryBuilder->withCount(3);
        $queryBuilder->withDateRange(new DateRange(
            new DateTime('2000-01-01 00:00:00'),
            new DateTime('2000-01-31 23:59:59')
        ));

        return [
            [
                $events,
                $queryBuilder->withCursor(Cursor::fromEvent($events['A']))->getQuery(),
                5, ['B', 'C', 'D'],
            ],
            [
                $events,
                $queryBuilder->withCursor(Cursor::fromEvent($events['F']))->getQuery(),
                0, [],
            ],
            [
                $events,
                $queryBuilder->withCursor(Cursor::fromEvent($events['C']))->getQuery(),
                3, ['D', 'E', 'F'],
            ],
        ];
    }

    public function dataProviderForDateRangeCriteriaTest(): array
    {
        $events = $this->createEvents([
            'A' => '2000-01-01',
            'B' => '2000-01-02',
            'C' => '2000-01-03',
            'D' => '2000-01-04',
            'E' => '2000-01-05',
            'F' => '2000-01-06',
        ]);

        $queryBuilder = new EventQueryBuilder();

        return [
            [
                $events,
                $queryBuilder->withDateRange(new DateRange(
                    new DateTime('1999-01-01 00:00:00'),
                    new DateTime('1999-01-31 23:59:59')
                ))->getQuery(),
                0,
                [],
            ],
            [
                $events,
                $queryBuilder->withDateRange(new DateRange(
                    new DateTime('2001-01-01 00:00:00'),
                    new DateTime('2001-01-31 23:59:59')
                ))->getQuery(),
                0,
                [],
            ],
            [
                $events,
                $queryBuilder->withDateRange(new DateRange(
                    new DateTime('2000-01-01 00:00:00'),
                    new DateTime('2000-01-01 00:00:00')
                ))->getQuery(),
                0,
                [],
            ],
            [
                $events,
                $queryBuilder->withDateRange(new DateRange(
                    new DateTime('2000-01-01 00:00:00'),
                    new DateTime('2000-01-01 23:59:59')
                ))->getQuery(),
                1,
                ['A'],
            ],
            [
                $events,
                $queryBuilder->withDateRange(new DateRange(
                    new DateTime('2000-01-01 00:00:00'),
                    new DateTime('2000-01-06 23:59:59')
                ))->getQuery(),
                6,
                ['A', 'B', 'C', 'D', 'E', 'F'],
            ],
            [
                $events,
                $queryBuilder->withDateRange(new DateRange(
                    new DateTime('2000-01-01 00:00:00'),
                    new DateTime('2000-01-06 23:59:59')
                ))->withCount(3)->getQuery(),
                6,
                ['A', 'B', 'C'],
            ],
        ];
    }

    abstract protected function createEventsSource(array $events): EventSourceInterface;

    /**
     * Asserts that $source contains $expected number of events matching given $query.
     *
     * @param int $expected
     * @param \Ibexa\Contracts\Calendar\EventSource\EventSourceInterface $source
     * @param \Ibexa\Contracts\Calendar\EventQuery $query
     *
     * @see \Ibexa\Contracts\Calendar\EventSource\EventSourceInterface::getCount
     */
    protected function assertCountForQuery(int $expected, EventSourceInterface $source, EventQuery $query): void
    {
        $this->assertEquals($expected, $source->getCount($query));
    }

    /**
     * Asserts that $source doesn't contains events matching given $query.
     *
     * @param \Ibexa\Contracts\Calendar\EventSource\EventSourceInterface $source
     * @param \Ibexa\Contracts\Calendar\EventQuery $query
     */
    protected function assertEmptyResultsForQuery(EventSourceInterface $source, EventQuery $query): void
    {
        $this->assertEquals(0, $source->getCount($query));
        $this->assertEmpty(iterator_to_array($source->getEvents($query)));
    }

    /**
     * Asserts that $source will return $expected events for given $query.
     *
     * @param \Ibexa\Contracts\Calendar\EventSource\EventSourceInterface $source
     * @param \Ibexa\Contracts\Calendar\EventQuery $query
     */
    protected function assertResultsForQuery(array $expected, EventSourceInterface $source, EventQuery $query): void
    {
        $actual = array_map(static function (Event $event) {
            return $event->getId();
        }, iterator_to_array($source->getEvents($query)));

        $this->assertEquals($expected, $actual);
    }

    private function doTestCriteria(array $events, EventQuery $query, int $expectedCount, array $expectedEvents): void
    {
        $source = $this->createEventsSource($events);

        $this->assertCountForQuery($expectedCount, $source, $query);
        $this->assertResultsForQuery($expectedEvents, $source, $query);
    }
}

class_alias(AbstractEventSourceTestCase::class, 'EzSystems\EzPlatformCalendar\Tests\Calendar\EventSource\AbstractEventSourceTestCase');
