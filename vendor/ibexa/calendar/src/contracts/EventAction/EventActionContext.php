<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Calendar\EventAction;

use Ibexa\Contracts\Calendar\EventCollection;

/**
 * Base class for action context. Context contains all necessary data to execute associated action.
 *
 * @see \Ibexa\Contracts\Calendar\EventAction\EventActionInterface::supports()
 */
abstract class EventActionContext
{
    /** @var \Ibexa\Contracts\Calendar\EventCollection */
    private $events;

    public function __construct(EventCollection $events)
    {
        $this->events = $events;
    }

    public function getEvents(): EventCollection
    {
        return $this->events;
    }
}

class_alias(EventActionContext::class, 'EzSystems\EzPlatformCalendar\Calendar\EventAction\EventActionContext');
