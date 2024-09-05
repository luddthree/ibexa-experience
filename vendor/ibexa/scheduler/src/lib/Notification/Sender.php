<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Scheduler\Notification;

use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\NotificationService;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo;
use Ibexa\Contracts\Core\Repository\Values\Notification\CreateStruct;
use Ibexa\Contracts\Scheduler\ValueObject\ScheduledEntry;
use Ibexa\Scheduler\ValueObject\NotificationFactory;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @internal
 */
final class Sender implements SenderInterface
{
    /** @var \Ibexa\Contracts\Core\Repository\ContentService */
    private $contentService;

    /** @var \Ibexa\Contracts\Core\Repository\NotificationService */
    private $notificationService;

    /** @var \Ibexa\Scheduler\ValueObject\NotificationFactory */
    private $notificationFactory;

    /** @var \Symfony\Contracts\Translation\TranslatorInterface */
    private $translator;

    public function __construct(
        ContentService $contentService,
        NotificationService $notificationService,
        NotificationFactory $notificationFactory,
        TranslatorInterface $translator
    ) {
        $this->contentService = $contentService;
        $this->notificationService = $notificationService;
        $this->notificationFactory = $notificationFactory;
        $this->translator = $translator;
    }

    public function sendPublishNotifications(
        ScheduledEntry $scheduledEntry,
        ContentInfo $contentInfo
    ): void {
        $createStruct = $this->notificationFactory->getNotificationCreateStruct(
            $scheduledEntry,
            NotificationFactory::TYPE_PUBLISHED
        );

        $createStruct->data['message'] = $this->translator->trans(
            /** @Desc("Content published") */
            'content.published',
            [],
            'ibexa_scheduler'
        );

        $this->notifySubscribers($contentInfo, $scheduledEntry, $createStruct);
    }

    public function sendHideNotifications(
        ScheduledEntry $scheduledEntry,
        ContentInfo $contentInfo
    ): void {
        $createStruct = $this->notificationFactory->getNotificationCreateStruct(
            $scheduledEntry,
            NotificationFactory::TYPE_HIDDEN
        );
        $createStruct->data['message'] = $this->translator->trans(
            /** @Desc("Content hidden") */
            'content.hidden',
            [],
            'ibexa_scheduler'
        );

        $this->notifySubscribers($contentInfo, $scheduledEntry, $createStruct);
    }

    private function notifySubscribers(
        ContentInfo $contentInfo,
        ScheduledEntry $scheduledEntry,
        CreateStruct $baseNotificationCreateStruct
    ): void {
        $subscribers = $this->getScheduleSubscribers($contentInfo, $scheduledEntry);

        foreach ($subscribers as $subscriber) {
            $notification = clone $baseNotificationCreateStruct;
            $notification->ownerId = $subscriber;

            $this->notificationService->createNotification($notification);
        }
    }

    private function getScheduleSubscribers(
        ContentInfo $contentInfo,
        ScheduledEntry $scheduledEntry
    ): array {
        $versions = $this->contentService->loadVersions($contentInfo);

        $subscribers = [$scheduledEntry->user->id];
        foreach ($versions as $version) {
            if ($version->status === VersionInfo::STATUS_DRAFT) {
                $subscribers[] = $version->creatorId;
            }
        }
        $subscribers = array_unique($subscribers);

        return $subscribers;
    }
}

class_alias(Sender::class, 'EzSystems\DateBasedPublisher\Core\Notification\Sender');
