<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Scheduler\Calendar;

use Ibexa\Calendar\EventAction\EventActionCollection;
use Ibexa\Contracts\Calendar\Event;
use Ibexa\Contracts\Calendar\EventType\EventTypeInterface;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Contracts\Translation\TranslatorInterface;

final class FuturePublicationEventType implements EventTypeInterface
{
    private const EVENT_TYPE_IDENTIFIER = 'future_publication';

    /** @var \Symfony\Contracts\Translation\TranslatorInterface */
    private $translator;

    /** @var \Ibexa\Contracts\Calendar\EventCollection */
    private $actions;

    public function __construct(iterable $actions, TranslatorInterface $translator)
    {
        $this->actions = new EventActionCollection($actions);
        $this->translator = $translator;
    }

    public function getTypeIdentifier(): string
    {
        return self::EVENT_TYPE_IDENTIFIER;
    }

    public function getTypeLabel(): string
    {
        return $this->translator->trans(
            /** @Desc("Content publication") */
            'future_publication.label',
            [],
            'ibexa_calendar_events'
        );
    }

    public function getEventName(Event $event): string
    {
        return $event->getScheduledEntry()->versionInfo->getName();
    }

    public function getActions(): EventActionCollection
    {
        return $this->actions;
    }
}

class_alias(FuturePublicationEventType::class, 'EzSystems\DateBasedPublisher\Core\Calendar\FuturePublicationEventType');
