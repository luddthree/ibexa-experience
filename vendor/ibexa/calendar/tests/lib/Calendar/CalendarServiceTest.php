<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Calendar\Calendar;

use DateTime;
use Ibexa\Calendar\CalendarService;
use Ibexa\Calendar\EventAction\EventActionCollection;
use Ibexa\Calendar\Exception\UnsupportedActionException;
use Ibexa\Contracts\Calendar\DateRange;
use Ibexa\Contracts\Calendar\EventAction\EventActionInterface;
use Ibexa\Contracts\Calendar\EventCollection;
use Ibexa\Contracts\Calendar\EventGroupList;
use Ibexa\Contracts\Calendar\EventQuery;
use Ibexa\Contracts\Calendar\EventSource\EventSourceInterface;
use Ibexa\Contracts\Calendar\EventType\EventTypeInterface;
use Ibexa\Tests\Calendar\Calendar\Stubs\TestEvent;
use Ibexa\Tests\Calendar\Calendar\Stubs\TestEventActionContext;
use Ibexa\User\UserSetting\UserSettingService;

class CalendarServiceTest extends AbstractCalendarTestCase
{
    /** @var \Ibexa\User\UserSetting\UserSettingService */
    private $userSettingsService;

    protected function setUp(): void
    {
        $this->userSettingsService = $this->createMock(UserSettingService::class);

        parent::setUp();
    }

    public function testExecuteAction(): void
    {
        $type = $this->createMock(EventTypeInterface::class);

        $context = new TestEventActionContext(
            new EventCollection($this->createEvents([
                'A' => '1970-01-01',
                'B' => '1975-03-21',
                'C' => '1985-11-05',
            ], $type))
        );

        $action = $this->createMock(EventActionInterface::class);
        $action->expects($this->once())->method('supports')->with($context)->willReturn(true);
        $action->expects($this->once())->method('execute')->with($context);

        $type->method('getActions')->willReturn(new EventActionCollection([$action]));

        $calendar = new CalendarService([]);
        $calendar->executeAction($context);
    }

    public function testExecuteActionForEmptyContext(): void
    {
        $type = $this->createMock(EventTypeInterface::class);

        $context = new TestEventActionContext(new EventCollection([]));

        $action = $this->createMock(EventActionInterface::class);
        $action->expects($this->never())->method('supports')->with($context);
        $action->expects($this->never())->method('execute')->with($context);

        $type->method('getActions')->willReturn(new EventActionCollection([$action]));

        $calendar = new CalendarService([]);
        $calendar->executeAction($context);
    }

    public function testExecuteActionThrowsUnsupportedActionException(): void
    {
        $this->expectException(UnsupportedActionException::class);

        $type = $this->createMock(EventTypeInterface::class);

        $context = new TestEventActionContext(
            new EventCollection($this->createEvents([
                'A' => '1970-01-01',
                'B' => '1975-03-21',
                'C' => '1985-11-05',
            ], $type))
        );

        $action = $this->createMock(EventActionInterface::class);
        $action->expects($this->once())->method('supports')->with($context)->willReturn(false);
        $action->expects($this->never())->method('execute')->with($context);

        $type->method('getActions')->willReturn(new EventActionCollection([$action]));

        $calendar = new CalendarService([]);
        $calendar->executeAction($context);
    }

    public function testLoadEvents(): void
    {
        $eventsIds = array_reverse([
            'A', 'B', 'C', 'D', 'E', 'F', 'X', 'Y', 'Z',
        ]);

        $foo = $this->createEventSourceMockForLoadEvents($eventsIds, [
            'A' => '1970-01-01',
            'B' => '1975-03-21',
        ]);

        $bar = $this->createEventSourceMockForLoadEvents($eventsIds, [
            'C' => '1985-11-05',
            'D' => '1995-01-01',
        ]);

        $baz = $this->createEventSourceMockForLoadEvents($eventsIds, [
            'E' => '2000-01-01',
            'F' => '2010-05-30',
        ]);

        $calendar = new CalendarService([$foo, $bar, $baz]);
        $events = $calendar->loadEvents($eventsIds);

        $this->assertEvents(['A', 'B', 'C', 'D', 'E', 'F'], $events);
    }

    public function testGetEvents(): void
    {
        $query = new EventQuery(new DateRange(
            new DateTime('1970-01-01'),
            new DateTime('2038-01-01')
        ));

        $foo = $this->createEventSourceMockForEventQuery($query, 4, [
            'A' => '1970-01-01',
            'C' => '1980-02-13',
            'E' => '1990-01-01',
            'H' => '2005-07-13',
        ]);
        $bar = $this->createEmptyEventSourceMockForEventQuery($query);
        $baz = $this->createEventSourceMockForEventQuery($query, 6, [
            'B' => '1975-03-21',
            'D' => '1985-11-05',
            'F' => '1995-01-01',
            'G' => '2000-01-01',
            'I' => '2010-05-30',
            'J' => '2038-01-01',
        ]);

        $calendar = new CalendarService([$foo, $bar, $baz], $this->userSettingsService);
        $events = $calendar->getEvents($query);

        $this->assertEquals(10, $events->getTotalCount());
        $this->assertEvents(range('A', 'J'), $events->getEvents());
    }

    /**
     * @dataProvider providerForGetGroupedEventsByDay
     */
    public function testGetGroupedEventsByDay(
        array $expectedResults,
        string $start,
        string $finish,
        string $timezone
    ): void {
        $sources = [
            'foo' => $this->createMock(EventSourceInterface::class),
            'bar' => $this->createMock(EventSourceInterface::class),
        ];

        $defaultTimezone = date_default_timezone_get();
        date_default_timezone_set($timezone);

        foreach ($sources as $name => $source) {
            $source->method('getCount')->willReturn(1);
            $source->method('getEvents')->willReturnCallback(function (EventQuery $query) use ($name) {
                $eventDate = $query->getDateRange()->getStartDate();
                $eventId = $name . '_' . $eventDate->format('Y_m_d');

                return [
                    new TestEvent($this->type, $eventId, $eventDate),
                ];
            });
        }

        $calendar = new CalendarService($sources);

        $actualResults = $calendar->getGroupedEvents(
            new EventQuery(new DateRange(
                new DateTime($start),
                new DateTime($finish)
            )),
            CalendarService::GROUP_BY_DAY
        );

        // Set server's timezone back
        date_default_timezone_set($defaultTimezone);

        $this->assertEventGroupList($expectedResults, $actualResults);
    }

    public function providerForGetGroupedEventsByDay(): iterable
    {
        yield 'Greenwich without daylight saving time change' => [
            'results' => [
                '[2019-05-06T00:00:00+0000 - 2019-05-07T00:00:00+0000]' => [
                    'foo_2019_05_06',
                    'bar_2019_05_06',
                ],
                '[2019-05-07T00:00:00+0000 - 2019-05-08T00:00:00+0000]' => [
                    'foo_2019_05_07',
                    'bar_2019_05_07',
                ],
                '[2019-05-08T00:00:00+0000 - 2019-05-09T00:00:00+0000]' => [
                    'foo_2019_05_08',
                    'bar_2019_05_08',
                ],
                '[2019-05-09T00:00:00+0000 - 2019-05-10T00:00:00+0000]' => [
                    'foo_2019_05_09',
                    'bar_2019_05_09',
                ],
                '[2019-05-10T00:00:00+0000 - 2019-05-11T00:00:00+0000]' => [
                    'foo_2019_05_10',
                    'bar_2019_05_10',
                ],
            ],
            'start' => '2019-05-06',
            'finish' => '2019-05-10',
            'timezone' => 'Greenwich',
        ];

        yield 'Europe/Berlin with daylight saving time change' => [
            'results' => [
                '[2023-03-23T23:00:00+0000 - 2023-03-24T23:00:00+0000]' => [
                    'foo_2023_03_23',
                    'bar_2023_03_23',
                ],
                '[2023-03-24T23:00:00+0000 - 2023-03-25T23:00:00+0000]' => [
                    'foo_2023_03_24',
                    'bar_2023_03_24',
                ],
                '[2023-03-25T23:00:00+0000 - 2023-03-26T23:00:00+0000]' => [
                    'foo_2023_03_25',
                    'bar_2023_03_25',
                ],
                '[2023-03-26T23:00:00+0000 - 2023-03-27T23:00:00+0000]' => [
                    'foo_2023_03_26',
                    'bar_2023_03_26',
                ],
                '[2023-03-27T23:00:00+0000 - 2023-03-28T23:00:00+0000]' => [
                    'foo_2023_03_27',
                    'bar_2023_03_27',
                ],
            ],
            'start' => '2023-03-24',
            'finish' => '2023-03-28',
            'timezone' => 'Europe/Berlin',
        ];
    }

    private function assertEventGroupList($expectedValue, EventGroupList $eventGroupList): void
    {
        $keys = array_keys($expectedValue);

        foreach ($eventGroupList as $i => $group) {
            $expectedKey = $keys[$i];

            $this->assertEquals($expectedKey, (string)$group->getGroupKey());
            $this->assertEvents($expectedValue[$expectedKey], $group);
        }
    }

    private function createEmptyEventSourceMockForEventQuery(EventQuery $query): EventSourceInterface
    {
        return $this->createEventSourceMockForEventQuery($query, 0, []);
    }

    private function createEventSourceMockForLoadEvents(array $loadedEventIds, array $expectedEvents): EventSourceInterface
    {
        $source = $this->createMock(EventSourceInterface::class);
        $source
            ->method('loadEvents')
            ->with($loadedEventIds)
            ->willReturn($this->createEvents($expectedEvents));

        return $source;
    }

    /**
     * @param \Ibexa\Contracts\Calendar\EventQuery $query
     * @param int $expectedCount
     * @param array $expectedEvents
     *
     * @return \Ibexa\Contracts\Calendar\EventSource\EventSourceInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private function createEventSourceMockForEventQuery(
        EventQuery $query,
        int $expectedCount,
        array $expectedEvents
    ): EventSourceInterface {
        $source = $this->createMock(EventSourceInterface::class);
        $source
            ->method('getCount')
            ->with($query)
            ->willReturn($expectedCount);

        if ($expectedCount > 0) {
            $source
                ->expects($this->once())
                ->method('getEvents')
                ->with($query)
                ->willReturn($this->createEvents($expectedEvents));
        } else {
            $source->expects($this->never())->method('getEvents');
        }

        return $source;
    }
}

class_alias(CalendarServiceTest::class, 'EzSystems\EzPlatformCalendar\Tests\Calendar\CalendarServiceTest');
