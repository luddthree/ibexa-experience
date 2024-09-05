<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Calendar\Calendar;

use DateTime;
use Ibexa\Contracts\Calendar\Cursor;
use Ibexa\Tests\Calendar\Calendar\Stubs\TestEvent;
use Ibexa\Tests\Calendar\Calendar\Stubs\TestEventType;
use PHPUnit\Framework\TestCase;

class CursorTest extends TestCase
{
    private const CURSOR_DATA = 'MTU1NjY2ODgwMDp0ZXN0OjAwODVlOTQ4LTg2ZDEtMTFlOS1iYzQyLTUyNmFmNzc2NGY2NA==';

    public function testFromEvent(): void
    {
        $expectedEventId = '7aa894b3-92e4-47f8-91c1-26be3d16044c';
        $expectedEventType = 'holiday';
        $expectedDateTime = new DateTime('2019-01-01');

        $event = new TestEvent(
            new TestEventType($expectedEventType),
            $expectedEventId,
            $expectedDateTime
        );

        $cursor = Cursor::fromEvent($event);

        $this->assertEquals($expectedEventId, $cursor->getEventId());
        $this->assertEquals($expectedDateTime, $cursor->getDateTime());
        $this->assertEquals($expectedEventType, $cursor->getEventType());
    }

    public function testFromString(): void
    {
        $expectedEventId = '0085e948-86d1-11e9-bc42-526af7764f64';
        $expectedEventType = 'test';
        $expectedDateTime = new DateTime('2019-05-01');

        $cursor = Cursor::fromString(self::CURSOR_DATA);

        $this->assertEquals($expectedEventId, $cursor->getEventId());
        $this->assertEquals($expectedDateTime, $cursor->getDateTime());
        $this->assertEquals($expectedEventType, $cursor->getEventType());
    }
}

class_alias(CursorTest::class, 'EzSystems\EzPlatformCalendar\Tests\Calendar\CursorTest');
