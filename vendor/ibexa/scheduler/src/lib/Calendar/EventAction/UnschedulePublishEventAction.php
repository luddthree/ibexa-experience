<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Scheduler\Calendar\EventAction;

use Ibexa\Contracts\Calendar\EventAction\EventActionContext;
use Ibexa\Contracts\Calendar\EventAction\EventActionInterface;
use Ibexa\Contracts\Scheduler\Repository\DateBasedPublishServiceInterface;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Contracts\Translation\TranslatorInterface;

final class UnschedulePublishEventAction implements EventActionInterface
{
    /** @var \Ibexa\Contracts\Scheduler\Repository\DateBasedPublishServiceInterface */
    private $dateBasedPublishService;

    private const EVENT_ACTION_IDENTIFIER = 'unschedule';

    /** @var \Symfony\Contracts\Translation\TranslatorInterface */
    private $translator;

    public function __construct(
        DateBasedPublishServiceInterface $dateBasedPublishService,
        TranslatorInterface $translator
    ) {
        $this->dateBasedPublishService = $dateBasedPublishService;
        $this->translator = $translator;
    }

    public function getActionIdentifier(): string
    {
        return self::EVENT_ACTION_IDENTIFIER;
    }

    public function getActionLabel(): string
    {
        return $this->translator->trans(
            /** @Desc("Cancel publication") */
            'future_publication.action.unschedule.label',
            [],
            'ibexa_calendar_events'
        );
    }

    public function supports(EventActionContext $context): bool
    {
        return $context instanceof UnscheduleEventActionContext;
    }

    /**
     * @param \Ibexa\Scheduler\Calendar\EventAction\UnscheduleEventActionContext $context
     */
    public function execute(EventActionContext $context): void
    {
        /** @var \Ibexa\Scheduler\Calendar\ScheduledEntryBasedEvent $event */
        foreach ($context->getEvents() as $event) {
            $this->dateBasedPublishService->unschedulePublish(
                $event->getScheduledEntry()->versionInfo->id
            );
        }
    }
}

class_alias(UnschedulePublishEventAction::class, 'EzSystems\DateBasedPublisher\Core\Calendar\EventAction\UnschedulePublishEventAction');
