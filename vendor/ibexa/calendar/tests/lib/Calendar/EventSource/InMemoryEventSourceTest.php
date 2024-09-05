<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Calendar\Calendar\EventSource;

use Ibexa\Contracts\Calendar\EventCollection;
use Ibexa\Contracts\Calendar\EventSource\EventSourceInterface;
use Ibexa\Contracts\Calendar\EventSource\InMemoryEventSource;

class InMemoryEventSourceTest extends AbstractEventSourceTestCase
{
    protected function createEventsSource(array $events): EventSourceInterface
    {
        return new InMemoryEventSource(new EventCollection($events));
    }
}

class_alias(InMemoryEventSourceTest::class, 'EzSystems\EzPlatformCalendar\Tests\Calendar\EventSource\InMemoryEventSourceTest');
