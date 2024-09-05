<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Calendar\Calendar\EventType;

use Ibexa\Calendar\EventType\EventTypeRegistry;
use Ibexa\Tests\Calendar\Calendar\Stubs\TestEventType;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class EventTypeRegistryTest extends TestCase
{
    public function testConstruct(): void
    {
        $foo = new TestEventType('foo');
        $bar = new TestEventType('bar');
        $baz = new TestEventType('baz');

        $types = [$foo, $bar, $baz];

        $this->assertEquals($types, array_values((new EventTypeRegistry($types))->getTypes()));
    }

    public function testHasType(): void
    {
        $registry = new EventTypeRegistry([
            new TestEventType('foo'),
            new TestEventType('bar'),
            new TestEventType('baz'),
        ]);

        $this->assertTrue($registry->hasType('foo'));
        $this->assertFalse($registry->hasType('foobar'));
    }

    public function testGetType(): void
    {
        $foo = new TestEventType('foo');
        $bar = new TestEventType('bar');
        $baz = new TestEventType('baz');

        $registry = new EventTypeRegistry([
            $foo, $bar, $baz,
        ]);

        $this->assertEquals($foo, $registry->getType('foo'));
    }

    public function testGetTypeThrowsInvalidArgumentException(): void
    {
        $foo = new TestEventType('foo');
        $bar = new TestEventType('bar');
        $baz = new TestEventType('baz');

        $registry = new EventTypeRegistry([
            $foo, $bar, $baz,
        ]);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Event Type named "foobar" does not exist.');

        $type = $registry->getType('foobar');
    }

    public function testRegister(): void
    {
        $bar = new TestEventType('bar');

        $registry = new EventTypeRegistry([
            new TestEventType('foo'),
        ]);
        $registry->register($bar);

        $this->assertTrue($registry->hasType('bar'));
        $this->assertEquals($bar, $registry->getType('bar'));
    }
}

class_alias(EventTypeRegistryTest::class, 'EzSystems\EzPlatformCalendar\Tests\Calendar\EventType\EventTypeRegistryTest');
