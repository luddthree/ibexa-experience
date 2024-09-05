<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Calendar\Calendar\Stubs;

use DateTime;
use DateTimeInterface;
use Ibexa\Contracts\Calendar\Event;
use Ibexa\Contracts\Calendar\EventType\EventTypeInterface;

class TestEvent extends Event
{
    public function __construct(EventTypeInterface $type, ?string $id = null, ?DateTimeInterface $dateTime = null)
    {
        if ($id === null) {
            $id = uniqid('event_');
        }

        if ($dateTime === null) {
            $dateTime = new DateTime();
        }

        parent::__construct($type, $id, $dateTime);
    }
}

class_alias(TestEvent::class, 'EzSystems\EzPlatformCalendar\Tests\Calendar\Stubs\TestEvent');
