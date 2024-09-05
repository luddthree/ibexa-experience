<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Scheduler\Calendar\EventAction;

use DateTime;
use Ibexa\Contracts\Calendar\EventAction\EventActionContext;
use Ibexa\Contracts\Calendar\EventAction\EventActionInterface;
use Ibexa\Contracts\Scheduler\Repository\DateBasedHideServiceInterface;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Contracts\Translation\TranslatorInterface;

final class RescheduleHideEventAction implements EventActionInterface
{
    private const EVENT_ACTION_IDENTIFIER = 'reschedule';

    /** @var \Ibexa\Contracts\Scheduler\Repository\DateBasedHideServiceInterface */
    private $dateBasedHideService;

    /** @var \Symfony\Contracts\Translation\TranslatorInterface */
    private $translator;

    public function __construct(
        DateBasedHideServiceInterface $dateBasedHideService,
        TranslatorInterface $translator
    ) {
        $this->dateBasedHideService = $dateBasedHideService;
        $this->translator = $translator;
    }

    public function getActionIdentifier(): string
    {
        return self::EVENT_ACTION_IDENTIFIER;
    }

    public function getActionLabel(): string
    {
        return $this->translator->trans(
            /** @Desc("Reschedule") */
            'future_hide.action.reschedule.label',
            [],
            'ibexa_calendar_events'
        );
    }

    public function supports(EventActionContext $context): bool
    {
        return $context instanceof RescheduleEventActionContext;
    }

    /**
     * @param \Ibexa\Scheduler\Calendar\EventAction\RescheduleEventActionContext $context
     */
    public function execute(EventActionContext $context): void
    {
        $dateTime = DateTime::createFromImmutable($context->getDateTime());

        /** @var \Ibexa\Scheduler\Calendar\ScheduledEntryBasedEvent $event */
        foreach ($context->getEvents() as $event) {
            $this->dateBasedHideService->updateScheduledHide(
                $event->getScheduledEntry(),
                $dateTime
            );
        }
    }
}

class_alias(RescheduleHideEventAction::class, 'EzSystems\DateBasedPublisher\Core\Calendar\EventAction\RescheduleHideEventAction');
