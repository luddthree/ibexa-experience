<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Calendar\Calendar\Stubs;

use Ibexa\Calendar\EventAction\EventActionCollection;
use Ibexa\Contracts\Calendar\Event;
use Ibexa\Contracts\Calendar\EventType\EventTypeInterface;

final class TestEventType implements EventTypeInterface
{
    /** @var string */
    private $identifier;

    /** @var \Ibexa\Contracts\Calendar\EventAction\EventActionInterface[] */
    private $actions;

    public function __construct(string $identifier = 'test', array $actions = [])
    {
        $this->identifier = $identifier;
        $this->actions = $actions;
    }

    public function getTypeIdentifier(): string
    {
        return $this->identifier;
    }

    public function getTypeLabel(): string
    {
        return $this->identifier;
    }

    public function getEventName(Event $event): string
    {
        return $event->getId();
    }

    public function getActions(): EventActionCollection
    {
        return new EventActionCollection($this->actions);
    }
}

class_alias(TestEventType::class, 'EzSystems\EzPlatformCalendar\Tests\Calendar\Stubs\TestEventType');
