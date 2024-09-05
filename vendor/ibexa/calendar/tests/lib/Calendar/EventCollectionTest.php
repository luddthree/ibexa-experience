<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Calendar\Calendar;

use ArrayIterator;
use Ibexa\Contracts\Calendar\Event;
use Ibexa\Contracts\Calendar\EventCollection;
use Ibexa\Tests\Calendar\Calendar\Stubs\TestEvent;
use Ibexa\Tests\Calendar\Calendar\Stubs\TestEventType;
use PHPUnit\Framework\TestCase;

class EventCollectionTest extends TestCase
{
    /** @var \Ibexa\Contracts\Calendar\EventType\EventTypeInterface */
    protected $type;

    protected function setUp(): void
    {
        $this->type = new TestEventType();
    }

    public function testIsEmptyReturnsTrue(): void
    {
        $collection = new EventCollection();

        $this->assertTrue($collection->isEmpty());
    }

    public function testIsEmptyReturnsFalse(): void
    {
        $collection = new EventCollection([new TestEvent($this->type)]);

        $this->assertFalse($collection->isEmpty());
    }

    public function testFirst(): void
    {
        $a = new TestEvent($this->type, 'a');
        $b = new TestEvent($this->type, 'b');
        $c = new TestEvent($this->type, 'c');

        $collection = new EventCollection([$a, $b, $c]);

        $this->assertEquals($a, $collection->first());
    }

    public function testLast(): void
    {
        $a = new TestEvent($this->type, 'a');
        $b = new TestEvent($this->type, 'b');
        $c = new TestEvent($this->type, 'c');

        $collection = new EventCollection([$a, $b, $c]);

        $this->assertEquals($c, $collection->last());
    }

    public function testSlice(): void
    {
        $a = new TestEvent($this->type, 'a');
        $b = new TestEvent($this->type, 'b');
        $c = new TestEvent($this->type, 'c');
        $d = new TestEvent($this->type, 'd');

        $collection = new EventCollection([$a, $b, $c, $d]);

        $this->assertEquals(new EventCollection([$b, $c, $d]), $collection->slice(1));
        $this->assertEquals(new EventCollection([$c, $d]), $collection->slice(2, 2));
        $this->assertEquals(new EventCollection(), $collection->slice(10, 1));
    }

    public function testFind(): void
    {
        $a = new TestEvent($this->type, 'a');
        $b = new TestEvent($this->type, 'b');
        $c = new TestEvent($this->type, 'c');

        $collection = new EventCollection([$a, $b, $c]);

        $this->assertNull($collection->find($this->getContraction()));
        $this->assertEquals(0, $collection->find($this->getTautology()));
        $this->assertEquals(1, $collection->find($this->getFindByIdPredicate('b')));
    }

    public function testFilter(): void
    {
        $a = new TestEvent($this->type, 'a');
        $b = new TestEvent($this->type, 'b');
        $c = new TestEvent($this->type, 'c');

        $collection = new EventCollection([$a, $b, $c]);

        $this->assertEquals(new EventCollection(), $collection->filter($this->getContraction()));
        $this->assertEquals($collection, $collection->filter($this->getTautology()));
    }

    public function testMap(): void
    {
        $a = new TestEvent($this->type, 'a');
        $b = new TestEvent($this->type, 'b');
        $c = new TestEvent($this->type, 'c');

        $collection = new EventCollection([$a, $b, $c]);

        $this->assertEquals(['A', 'B', 'C'], $collection->map(static function (Event $event) {
            return strtoupper($event->getId());
        }));
    }

    public function testCount(): void
    {
        $a = new TestEvent($this->type, 'a');
        $b = new TestEvent($this->type, 'b');
        $c = new TestEvent($this->type, 'c');

        $collection = new EventCollection([$a, $b, $c]);

        $this->assertEquals(3, $collection->count());
    }

    public function testFromIterator(): void
    {
        $array = [
            $a = new TestEvent($this->type, 'a'),
            $b = new TestEvent($this->type, 'b'),
            $c = new TestEvent($this->type, 'c'),
        ];

        $iterator = new ArrayIterator($array);

        $this->assertEquals(
            new EventCollection([$a, $b, $c]),
            EventCollection::fromIterator($iterator)
        );
    }

    public function testOf(): void
    {
        $a = new TestEvent($this->type, 'a');
        $b = new TestEvent($this->type, 'b');
        $c = new TestEvent($this->type, 'c');

        $this->assertEquals(new EventCollection([$a, $b, $c]), EventCollection::of($a, $b, $c));
    }

    /**
     * Returns predicate with is always true.
     *
     * @return callable
     */
    private function getTautology(): callable
    {
        return static function (TestEvent $event) {
            return true;
        };
    }

    /**
     * Returns predicate with is always false.
     *
     * @return callable
     */
    private function getContraction(): callable
    {
        return static function (TestEvent $event) {
            return false;
        };
    }

    private function getFindByIdPredicate(string $id)
    {
        return static function (TestEvent $event) use ($id) {
            return $event->getId() === $id;
        };
    }
}

class_alias(EventCollectionTest::class, 'EzSystems\EzPlatformCalendar\Tests\Calendar\EventCollectionTest');
