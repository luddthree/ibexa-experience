<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Calendar;

use DateTimeImmutable;
use DateTimeInterface;
use Ibexa\Calendar\Exception\UnsupportedActionException;
use Ibexa\Contracts\Calendar\EventAction\EventActionContext;
use Ibexa\Contracts\Calendar\EventType\EventTypeInterface;

/**
 * Base class for calendar events.
 *
 * See {@link https://doc.ibexa.co/en/latest/administration/back_office/customize_calendar/#configure-custom-events documentation}
 */
abstract class Event
{
    /** @var string */
    private $id;

    /** @var \DateTimeImmutable */
    private $dateTime;

    /** @var \Ibexa\Contracts\Calendar\EventType\EventTypeInterface */
    private $type;

    public function __construct(EventTypeInterface $type, string $id, DateTimeInterface $dateTime)
    {
        if (!($dateTime instanceof DateTimeImmutable)) {
            $dateTime = DateTimeImmutable::createFromFormat('U', (string)$dateTime->getTimestamp(), $dateTime->getTimezone());
        }

        $this->type = $type;
        $this->id = $id;
        $this->dateTime = $dateTime;
    }

    public function getType(): EventTypeInterface
    {
        return $this->type;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getDateTime(): DateTimeInterface
    {
        return $this->dateTime;
    }

    /**
     * Returns human-readable label of the event. For example: "Birthday of John".
     */
    public function getName(): string
    {
        return $this->getType()->getEventName($this);
    }

    /**
     * Returns true if the $this is after given $event.
     *
     * @param \Ibexa\Contracts\Calendar\Event $event
     */
    public function isAfter(self $event): bool
    {
        return $event->getDateTime() < $this->getDateTime();
    }

    /**
     * Returns true if the $this is before given $event.
     *
     * @param \Ibexa\Contracts\Calendar\Event $event
     */
    public function isBefore(self $event): bool
    {
        return $event->getDateTime() > $this->getDateTime();
    }

    /**
     * Compares $this with given $event.
     *
     * @param \Ibexa\Contracts\Calendar\Event $event
     */
    public function compareTo(self $event): int
    {
        return $this->getDateTime() <=> $event->getDateTime();
    }

    public function __toString()
    {
        return $this->getName();
    }

    protected function executeAction(EventActionContext $context): void
    {
        foreach ($this->getType()->getActions() as $action) {
            if ($action->supports($context)) {
                $action->execute($context);

                return;
            }
        }

        throw new UnsupportedActionException('Unsupported action definition found for: ' . get_class($context));
    }
}

class_alias(Event::class, 'EzSystems\EzPlatformCalendar\Calendar\Event');
