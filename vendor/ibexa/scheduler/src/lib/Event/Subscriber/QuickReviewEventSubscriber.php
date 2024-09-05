<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Scheduler\Event\Subscriber;

use Ibexa\Contracts\Core\Repository\NotificationService;
use Ibexa\Contracts\Scheduler\Repository\DateBasedPublishServiceInterface;
use Ibexa\Scheduler\ValueObject\NotificationFactory;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\CompletedEvent;
use Symfony\Contracts\Translation\TranslatorInterface;

class QuickReviewEventSubscriber implements EventSubscriberInterface
{
    /** @var \Ibexa\Contracts\Scheduler\Repository\DateBasedPublishServiceInterface */
    private $dateBasedPublisherService;

    /** @var \Ibexa\Contracts\Core\Repository\NotificationService */
    private $notificationService;

    /** @var \Ibexa\Scheduler\ValueObject\NotificationFactory */
    private $notificationFactory;

    /** @var \Symfony\Contracts\Translation\TranslatorInterface */
    private $translator;

    public function __construct(
        DateBasedPublishServiceInterface $dateBasedPublisherService,
        NotificationService $notificationService,
        NotificationFactory $notificationFactory,
        TranslatorInterface $translator
    ) {
        $this->dateBasedPublisherService = $dateBasedPublisherService;
        $this->notificationService = $notificationService;
        $this->notificationFactory = $notificationFactory;
        $this->translator = $translator;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'workflow.quick_review.completed' => ['onQuickReview', 0],
        ];
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    public function onQuickReview(CompletedEvent $event)
    {
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Content $content */
        $content = $event->getSubject();

        $scheduledVersions = $this->dateBasedPublisherService->getVersionsEntriesForContent($content->id);

        /** @var \Ibexa\Contracts\Scheduler\ValueObject\ScheduledEntry $scheduledVersion */
        foreach ($scheduledVersions as $scheduledVersion) {
            $this->dateBasedPublisherService->unschedulePublish(
                $scheduledVersion->versionInfo->id
            );

            $createStruct = $this->notificationFactory->getNotificationCreateStruct($scheduledVersion, NotificationFactory::TYPE_UNSCHEDULED);
            $createStruct->data['message'] = $this->translator->trans(/** @Desc("Content sent to review") */
                'content.in_review',
                [],
                'ibexa_scheduler'
            );

            $this->notificationService->createNotification($createStruct);
        }
    }
}

class_alias(QuickReviewEventSubscriber::class, 'EzSystems\DateBasedPublisher\Core\Event\Subscriber\QuickReviewEventSubscriber');
