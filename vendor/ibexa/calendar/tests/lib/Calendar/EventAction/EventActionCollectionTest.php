<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Calendar\Calendar\EventAction;

use Ibexa\Contracts\Calendar\EventAction\EventActionCollection;
use Ibexa\Tests\Calendar\Calendar\Stubs\TestEventAction;
use PHPUnit\Framework\TestCase;

class EventActionCollectionTest extends TestCase
{
    public function testIsEmptyReturnsTrue(): void
    {
        $collection = new EventActionCollection();

        $this->assertTrue($collection->isEmpty());
    }

    public function testIsEmptyReturnsFalse(): void
    {
        $collection = new EventActionCollection([
            new TestEventAction(),
        ]);

        $this->assertFalse($collection->isEmpty());
    }

    public function testSupports(): void
    {
        $collection = new EventActionCollection([
            new TestEventAction('foo'),
        ]);

        $this->assertTrue($collection->supports('foo'));
        $this->assertFalse($collection->supports('bar'));
    }

    public function testGet(): void
    {
        $collection = new EventActionCollection([
            $foo = new TestEventAction('foo'),
        ]);

        $this->assertEquals($foo, $collection->get('foo'));
    }

    public function testGetThrowsInvalidArgumentException(): void
    {
        $this->expectExceptionMessage('Action foobar is not supported.');

        $collection = new EventActionCollection([
            new TestEventAction('foo'),
            new TestEventAction('bar'),
            new TestEventAction('baz'),
        ]);

        $collection->get('foobar');
    }

    public function testCreateEmpty(): void
    {
        $this->assertEquals(new EventActionCollection(), EventActionCollection::createEmpty());
    }
}

class_alias(EventActionCollectionTest::class, 'EzSystems\EzPlatformCalendar\Tests\Calendar\EventAction\EventActionCollectionTest');
