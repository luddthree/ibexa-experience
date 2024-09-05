<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Scheduler\Calendar;

use DateTimeInterface;
use Ibexa\Contracts\Calendar\Event;
use Ibexa\Contracts\Calendar\EventCollection;
use Ibexa\Contracts\Calendar\LanguageBasedEvent;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\Scheduler\ValueObject\ScheduledEntry;
use Ibexa\Scheduler\Calendar\EventAction\RescheduleEventActionContext;
use Ibexa\Scheduler\Calendar\EventAction\UnscheduleEventActionContext;

final class FuturePublicationEvent extends Event implements LanguageBasedEvent, ScheduledEntryBasedEvent
{
    /** @var \Ibexa\Contracts\Scheduler\ValueObject\ScheduledEntry */
    private $scheduledEntry;

    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Language */
    private $language;

    public function __construct(
        FuturePublicationEventType $type,
        string $id,
        ScheduledEntry $scheduledEntry,
        Language $language
    ) {
        parent::__construct($type, $id, $scheduledEntry->date);

        $this->scheduledEntry = $scheduledEntry;
        $this->language = $language;
    }

    public function getScheduledEntry(): ScheduledEntry
    {
        return $this->scheduledEntry;
    }

    public function getLanguage(): Language
    {
        return $this->language;
    }

    public function reschedule(DateTimeInterface $dateTime): void
    {
        $this->executeAction(new RescheduleEventActionContext(EventCollection::of($this), $dateTime));
    }

    public function unschedule(): void
    {
        $this->executeAction(new UnscheduleEventActionContext(EventCollection::of($this)));
    }
}

class_alias(FuturePublicationEvent::class, 'EzSystems\DateBasedPublisher\Core\Calendar\FuturePublicationEvent');
