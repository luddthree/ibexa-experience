<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\FieldTypePage\Calendar;

use DateTime;
use Ibexa\Contracts\Calendar\Cursor;
use Ibexa\Contracts\Calendar\Event;
use Ibexa\Contracts\Calendar\EventType\EventTypeInterface;
use Ibexa\FieldTypePage\Calendar\ScheduledEntryIdProvider;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class ScheduledEntryIdProviderTest extends TestCase
{
    private const TYPE_IDENTIFIER = '__TYPE_IDENTIFIER__';
    private const ENTRY_ID = '1';

    /** @var \Ibexa\FieldTypePage\Calendar\ScheduledEntryIdProvider */
    private $scheduledEntryIdProvider;

    /** @var \Ibexa\Contracts\Calendar\EventType\EventTypeInterface */
    private $eventType;

    public function setUp(): void
    {
        $this->eventType = $this->createMock(EventTypeInterface::class);
        $this->scheduledEntryIdProvider = new ScheduledEntryIdProvider();
    }

    public function providerForTestFromCursor(): iterable
    {
        yield 'when cursor and event has the same type identifier' => [
            self::TYPE_IDENTIFIER,
            self::TYPE_IDENTIFIER,
            self::ENTRY_ID,
        ];

        yield 'when cursor and event has different type identifier' => [
            self::TYPE_IDENTIFIER,
            '__OTHER_IDENTIFIER__',
            null,
        ];
    }

    /**
     * @dataProvider providerForTestFromCursor
     */
    public function testFromCursor(string $eventTypeIdentifier, string $cursorTypeIdentifier, ?string $expectedEntryId)
    {
        $this->eventType
            ->method('getTypeIdentifier')
            ->willReturn($eventTypeIdentifier);

        $cursor = $this->createCursor();
        $entryId = $this->scheduledEntryIdProvider->fromCursor($cursor, $cursorTypeIdentifier);

        Assert::assertEquals($expectedEntryId, $entryId);
    }

    public function testFromCursorWhenCursorIsEmpty()
    {
        $cursor = null;
        $entryId = $this->scheduledEntryIdProvider->fromCursor($cursor, self::TYPE_IDENTIFIER);

        Assert::assertNull($entryId);
    }

    private function createCursor(): Cursor
    {
        $eventId = sprintf('%s:%s', self::TYPE_IDENTIFIER, self::ENTRY_ID);
        $event = $this->createEvent($this->eventType, $eventId);

        return Cursor::fromEvent($event);
    }

    private function createEvent(EventTypeInterface $eventType, $id): Event
    {
        return new class($eventType, $id, new DateTime()) extends Event {
        };
    }
}

class_alias(ScheduledEntryIdProviderTest::class, 'EzSystems\EzPlatformPageFieldType\Tests\Calendar\ScheduledEntryIdProviderTest');
