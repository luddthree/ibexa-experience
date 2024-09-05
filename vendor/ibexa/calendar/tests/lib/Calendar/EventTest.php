<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Calendar\Calendar;

use DateTime;
use Ibexa\Tests\Calendar\Calendar\Stubs\TestEvent;
use Ibexa\Tests\Calendar\Calendar\Stubs\TestEventType;
use PHPUnit\Framework\TestCase;

class EventTest extends TestCase
{
    /** @var \Ibexa\Contracts\Calendar\EventType\EventTypeInterface */
    protected $type;

    protected function setUp(): void
    {
        $this->type = new TestEventType();
    }

    public function testIsBefore(): void
    {
        $a = new TestEvent($this->type, 'A', new DateTime('1900-01-01'));
        $b = new TestEvent($this->type, 'B', new DateTime('2000-01-01'));

        $this->assertTrue($a->isBefore($b));
        $this->assertFalse($b->isBefore($a));
        $this->assertFalse($a->isBefore($a));
    }

    public function testIsAfter(): void
    {
        $a = new TestEvent($this->type, 'A', new DateTime('1900-01-01'));
        $b = new TestEvent($this->type, 'B', new DateTime('2000-01-01'));

        $this->assertTrue($b->isAfter($a));
        $this->assertFalse($a->isAfter($b));
        $this->assertFalse($b->isAfter($b));
    }
}

class_alias(EventTest::class, 'EzSystems\EzPlatformCalendar\Tests\Calendar\EventTest');
