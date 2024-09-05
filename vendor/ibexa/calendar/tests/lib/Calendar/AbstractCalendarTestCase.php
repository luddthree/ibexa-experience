<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Calendar\Calendar;

use DateTime;
use Ibexa\Contracts\Calendar\Event;
use Ibexa\Contracts\Calendar\EventType\EventTypeInterface;
use Ibexa\Tests\Calendar\Calendar\Stubs\TestEvent;
use Ibexa\Tests\Calendar\Calendar\Stubs\TestEventType;
use PHPUnit\Framework\TestCase;

abstract class AbstractCalendarTestCase extends TestCase
{
    /** @var \Ibexa\Contracts\Calendar\EventType\EventTypeInterface */
    protected $type;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->type = new TestEventType();
    }

    /**
     * Create events from given hash (<id> => <datetime>).
     *
     * @param array $data
     * @param \Ibexa\Contracts\Calendar\EventType\EventTypeInterface|null $type
     *
     * @return \Ibexa\Contracts\Calendar\Event[]
     */
    protected function createEvents(array $data, EventTypeInterface $type = null): array
    {
        $events = [];
        foreach ($data as $id => $dateTime) {
            $events[$id] = new TestEvent($type ?? $this->type, $id, new DateTime($dateTime));
        }

        return $events;
    }

    protected function assertEvents(array $expected, iterable $actual): void
    {
        $actual = array_map(static function (Event $event) {
            return $event->getId();
        }, iterator_to_array($actual));

        $this->assertEquals($expected, $actual);
    }
}

class_alias(AbstractCalendarTestCase::class, 'EzSystems\EzPlatformCalendar\Tests\Calendar\AbstractCalendarTestCase');
