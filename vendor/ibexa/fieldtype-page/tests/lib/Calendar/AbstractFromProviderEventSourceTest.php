<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\FieldTypePage\Calendar;

use DateTime;
use Ibexa\Contracts\Calendar\Cursor;
use Ibexa\Contracts\Calendar\DateRange;
use Ibexa\Contracts\Calendar\Event;
use Ibexa\Contracts\Calendar\EventQuery;
use Ibexa\Contracts\Calendar\EventQueryBuilder;
use Ibexa\Contracts\Calendar\EventType\EventTypeInterface;
use Ibexa\FieldTypePage\Calendar\AbstractFromProviderEventSource;
use Ibexa\FieldTypePage\Calendar\Provider\EntriesProviderInterface;
use Ibexa\FieldTypePage\Calendar\ScheduledEntryIdProvider;
use Ibexa\FieldTypePage\Calendar\ScheduledEntryInterface;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class AbstractFromProviderEventSourceTest extends TestCase
{
    private const TYPE_IDENTIFIER = '__TYPE_IDENTIFIER__';
    private const ENTRY_ID = '1';

    /** @var \Ibexa\FieldTypePage\Calendar\AbstractFromProviderEventSource */
    private $fromProviderEventSource;

    /** @var \Ibexa\FieldTypePage\Calendar\Provider\EntriesProviderInterface */
    private $entriesProvider;

    /** @var \Ibexa\Contracts\Calendar\EventType\EventTypeInterface */
    private $eventType;

    /** @var \Ibexa\FieldTypePage\Calendar\ScheduledEntryInterface */
    private $scheduledEntry;

    /** @var \Ibexa\FieldTypePage\Calendar\ScheduledEntryIdProvider */
    private $scheduledEntryIdProvider;

    public function setUp(): void
    {
        $this->entriesProvider = $this->createMock(EntriesProviderInterface::class);
        $this->eventType = $this->createMock(EventTypeInterface::class);
        $this->scheduledEntry = $this->createMock(ScheduledEntryInterface::class);
        $this->scheduledEntryIdProvider = $this->createMock(ScheduledEntryIdProvider::class);

        $this->eventType
            ->method('getTypeIdentifier')
            ->willReturn(self::TYPE_IDENTIFIER);

        $this->scheduledEntry
            ->method('getId')
            ->willReturn(self::ENTRY_ID);

        $this->fromProviderEventSource = $this->createFromProviderEventSource();
    }

    public function providerForTestLoadEvents(): iterable
    {
        yield 'when IDs are prefixed' => [
            [
                sprintf('%s:%s', self::TYPE_IDENTIFIER, self::ENTRY_ID),
            ],
            sprintf('%s:%s', self::TYPE_IDENTIFIER, self::ENTRY_ID),
        ];

        yield 'when non-prefixed IDs should omit non-prefixed entries' => [
            [
                self::ENTRY_ID,
                sprintf('%s:%s', self::TYPE_IDENTIFIER, self::ENTRY_ID),
            ],
            sprintf('%s:%s', self::TYPE_IDENTIFIER, self::ENTRY_ID),
        ];
    }

    /**
     * @dataProvider providerForTestLoadEvents
     */
    public function testLoadEvents(array $eventsIdToFetch, string $expectedEventId)
    {
        $this->entriesProvider
            ->method('getScheduledEntriesByIds')
            ->with($this->equalTo([self::ENTRY_ID]))
            ->willReturn(
                [$this->scheduledEntry]
            );

        $eventsFetchedFromSource = $this->fromProviderEventSource->loadEvents($eventsIdToFetch);

        Assert::assertContainsOnlyInstancesOf(Event::class, $eventsFetchedFromSource);
        Assert::assertEquals($expectedEventId, $eventsFetchedFromSource[0]->getId());
        Assert::assertEquals(self::TYPE_IDENTIFIER, $eventsFetchedFromSource[0]->getType()->getTypeIdentifier());
    }

    public function providerForTestGetCount(): iterable
    {
        yield 'when not excluded identifier' => [
            self::TYPE_IDENTIFIER,
            1,
        ];

        yield 'when excluded identifier' => [
            '__EXCLUDED_IDENTIFIER__',
            0,
        ];
    }

    /**
     * @dataProvider providerForTestGetCount
     */
    public function testGetCount(string $eventTypeIdentifier, int $expectedNumberOfEvents)
    {
        $eventId = sprintf('%s:%s', self::TYPE_IDENTIFIER, self::ENTRY_ID);
        $event = $this->createEvent($this->eventType, $eventId);
        $query = $this->buildEventQuery(
            $eventTypeIdentifier,
            $fromDate = DateTime::createFromFormat('d/m/Y', '1/10/2020'),
            $toDate = DateTime::createFromFormat('d/m/Y', '15/10/2020'),
            $cursor = Cursor::fromEvent($event),
            $count = 10,
            $languages = ['eng-GB']
        );

        $this->scheduledEntryIdProvider
            ->method('fromCursor')
            ->with(
                $this->equalTo($cursor),
                $this->equalTo(self::TYPE_IDENTIFIER)
            )
            ->willReturn((int)self::ENTRY_ID);

        $this->entriesProvider
            ->method('countScheduledEntriesInDateRange')
            ->with(
                $this->equalTo($fromDate),
                $this->equalTo($toDate),
                $this->equalTo($languages),
                $this->equalTo((int)self::ENTRY_ID)
            )
        ->willReturn($expectedNumberOfEvents);

        Assert::assertEquals(
            $expectedNumberOfEvents,
            $this->fromProviderEventSource->getCount($query)
        );
    }

    public function testGetEventsForNotExcludedIdentifier()
    {
        $expectedEventId = sprintf('%s:%s', self::TYPE_IDENTIFIER, self::ENTRY_ID);
        $expectedEvent = $this->createEvent($this->eventType, $expectedEventId);
        $query = $this->buildEventQuery(
            self::TYPE_IDENTIFIER,
            $fromDate = DateTime::createFromFormat('d/m/Y', '1/10/2020'),
            $toDate = DateTime::createFromFormat('d/m/Y', '15/10/2020'),
            $cursor = Cursor::fromEvent($expectedEvent),
            $count = 10,
            $languages = ['eng-GB']
        );

        $this->scheduledEntryIdProvider
            ->method('fromCursor')
            ->with(
                $this->equalTo($cursor),
                $this->equalTo(self::TYPE_IDENTIFIER)
            )
            ->willReturn((int)self::ENTRY_ID);

        $this->entriesProvider
            ->method('getScheduledEntriesInDateRange')
            ->with(
                $this->equalTo($fromDate),
                $this->equalTo($toDate),
                $this->equalTo($languages),
                $this->equalTo((int)self::ENTRY_ID),
                $this->equalTo($count)
            )
            ->willReturn(
                [$this->scheduledEntry]
            );

        $eventsFetchedFromSource = $this->fromProviderEventSource->getEvents($query);

        Assert::assertContainsOnlyInstancesOf(Event::class, $eventsFetchedFromSource);
        Assert::assertEquals($expectedEventId, $eventsFetchedFromSource[0]->getId());
        Assert::assertEquals(self::TYPE_IDENTIFIER, $eventsFetchedFromSource[0]->getType()->getTypeIdentifier());
    }

    public function testGetEventsForExcludedIdentifier()
    {
        $expectedEventId = sprintf('%s:%s', '__EXCLUDED_IDENTIFIER__', self::ENTRY_ID);
        $expectedEvent = $this->createEvent($this->eventType, $expectedEventId);
        $query = $this->buildEventQuery(
            '__EXCLUDED_IDENTIFIER__',
            $fromDate = DateTime::createFromFormat('d/m/Y', '1/10/2020'),
            $toDate = DateTime::createFromFormat('d/m/Y', '15/10/2020'),
            $cursor = Cursor::fromEvent($expectedEvent),
            $count = 10,
            $languages = ['eng-GB']
        );

        $eventsFetchedFromSource = $this->fromProviderEventSource->getEvents($query);

        Assert::assertEmpty($eventsFetchedFromSource);
    }

    private function buildEventQuery(
        string $typeIdentifier,
        DateTime $fromDate,
        DateTime $toDate,
        ?Cursor $cursor,
        int $count,
        array $languages
    ): EventQuery {
        $queryBuilder = new EventQueryBuilder();
        $queryBuilder
            ->withTypes([$typeIdentifier])
            ->withDateRange(new DateRange($fromDate, $toDate))
            ->withCount($count)
            ->withLanguages($languages)
            ->withCursor($cursor);

        return $queryBuilder->getQuery();
    }

    private function createFromProviderEventSource(): AbstractFromProviderEventSource
    {
        return new class($this->entriesProvider, $this->eventType, $this->scheduledEntryIdProvider) extends AbstractFromProviderEventSource {
            protected function buildEvent(
                EventTypeInterface $eventType,
                ScheduledEntryInterface $scheduledEntry,
                string $eventId
            ): Event {
                return new class($eventType, $eventId, new DateTime()) extends Event {
                };
            }
        };
    }

    private function createEvent(EventTypeInterface $eventType, $id): Event
    {
        return new class($eventType, $id, new DateTime()) extends Event {
        };
    }
}

class_alias(AbstractFromProviderEventSourceTest::class, 'EzSystems\EzPlatformPageFieldType\Tests\Calendar\AbstractFromProviderEventSourceTest');
