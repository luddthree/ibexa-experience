<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Calendar\EventType;

use Ibexa\Contracts\Calendar\Event;
use Ibexa\Contracts\Calendar\EventAction\EventActionCollection;

/**
 * Definition of event type.
 */
interface EventTypeInterface
{
    /**
     * Returns event type identifier.
     */
    public function getTypeIdentifier(): string;

    /**
     * Returns human-readable label for event type.
     */
    public function getTypeLabel(): string;

    /**
     * Returns the name of the event.
     */
    public function getEventName(Event $event): string;

    /**
     * Returns actions supported by event type.
     */
    public function getActions(): EventActionCollection;
}

class_alias(EventTypeInterface::class, 'EzSystems\EzPlatformCalendar\Calendar\EventType\EventTypeInterface');
