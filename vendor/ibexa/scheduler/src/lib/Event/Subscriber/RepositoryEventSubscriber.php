<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Scheduler\Event\Subscriber;

use Ibexa\Contracts\Core\Repository\Events\Content\BeforeDeleteContentEvent;
use Ibexa\Contracts\Core\Repository\Events\Content\BeforeDeleteVersionEvent;
use Ibexa\Contracts\Core\Repository\Events\Content\UpdateContentEvent;
use Ibexa\Contracts\Core\Repository\Events\Trash\TrashEvent;
use Ibexa\Contracts\Core\Repository\NotificationService;
use Ibexa\Contracts\Core\Repository\Values\Notification\CreateStruct;
use Ibexa\Contracts\Scheduler\Repository\DateBasedPublishServiceInterface;
use Ibexa\Core\Base\Exceptions\NotFoundException;
use Ibexa\Scheduler\Persistence\HandlerInterface;
use Ibexa\Scheduler\Repository\DateBasedPublisherService;
use Ibexa\Scheduler\ValueObject\NotificationFactory;
use Ibexa\Scheduler\ValueObject\ScheduledEntry as SPIScheduledEntry;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class RepositoryEventSubscriber implements EventSubscriberInterface
{
    /** @var \Ibexa\Scheduler\Persistence\HandlerInterface */
    private $persistenceHandler;

    /** @var \Symfony\Contracts\Translation\TranslatorInterface */
    private $translator;

    /** @var \Ibexa\Scheduler\ValueObject\NotificationFactory */
    private $notificationFactory;

    /** @var \Ibexa\Contracts\Core\Repository\NotificationService */
    private $notificationService;

    /** @var \Ibexa\Contracts\Scheduler\Repository\DateBasedPublishServiceInterface */
    private $dateBasedPublisherService;

    public function __construct(
        HandlerInterface $persistenceHandler,
        TranslatorInterface $translator,
        NotificationFactory $notificationFactory,
        NotificationService $notificationService,
        DateBasedPublishServiceInterface $dateBasedPublisherService
    ) {
        $this->persistenceHandler = $persistenceHandler;
        $this->translator = $translator;
        $this->notificationFactory = $notificationFactory;
        $this->notificationService = $notificationService;
        $this->dateBasedPublisherService = $dateBasedPublisherService;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            BeforeDeleteContentEvent::class => 'onBeforeDeleteContent',
            BeforeDeleteVersionEvent::class => 'onBeforeDeleteVersion',
            UpdateContentEvent::class => 'onUpdateContent',
            TrashEvent::class => 'onTrash',
        ];
    }

    public function onBeforeDeleteContent(BeforeDeleteContentEvent $event): void
    {
        $contentInfo = $event->getContentInfo();
        $contentId = $contentInfo->id;

        $spiScheduledEntries = $this->persistenceHandler
            ->getAllTypesEntries($contentId, null, 0, null);

        $this->persistenceHandler->deleteAllTypesEntries($contentId, null);

        foreach ($spiScheduledEntries as $spiScheduledEntry) {
            $this->createNotificationForDelete($spiScheduledEntry);
        }
    }

    public function onBeforeDeleteVersion(BeforeDeleteVersionEvent $event): void
    {
        $versionInfo = $event->getVersionInfo();
        $versionId = $versionInfo->id;

        try {
            $scheduledVersion = $this->dateBasedPublisherService->getScheduledPublish($versionId);
        } catch (NotFoundException $e) {
            return;
        }

        $this->persistenceHandler->deleteVersionEntry(
            $versionId,
            DateBasedPublisherService::ACTION_TYPE
        );

        $contentId = $scheduledVersion->content->id;
        $versionNo = $scheduledVersion->versionInfo->versionNo;
        $notification = new CreateStruct();
        $notification->ownerId = $scheduledVersion->user->id;
        $notification->type = 'DateBasedPublisher:' . NotificationFactory::TYPE_UNSCHEDULED;
        $notification->data = [
            'message' => $this->translator->trans(/** @Desc("Version deleted") */
                'version.deleted',
                [],
                'ibexa_scheduler'
            ),
            'contentName' => sprintf(
                '[id: %d, version: %d]',
                $contentId,
                $versionNo
            ),
            'contentId' => $contentId,
            'versionNumber' => $versionNo,
            'isAvailable' => false,
            'link' => false,
        ];

        $this->notificationService->createNotification($notification);
    }

    public function onUpdateContent(UpdateContentEvent $event): void
    {
        $versionInfo = $event->getContent()->getVersionInfo();

        $isVersionScheduled = $this->dateBasedPublisherService->isScheduledPublish($versionInfo->id);
        if (!$isVersionScheduled) {
            return;
        }

        $scheduledVersion = $this->dateBasedPublisherService->getScheduledPublish($versionInfo->id);

        $this->dateBasedPublisherService->unschedulePublish($versionInfo->id);

        $createStruct = $this->notificationFactory->getNotificationCreateStruct($scheduledVersion, NotificationFactory::TYPE_UNSCHEDULED);
        $createStruct->data['message'] = $this->translator->trans(/** @Desc("Content updated") */
            'content.updated',
            [],
            'ibexa_scheduler'
        );

        $this->notificationService->createNotification($createStruct);
    }

    public function onTrash(TrashEvent $event): void
    {
        /** @var \Ibexa\Core\Repository\Values\Content\TrashItem|null $trashItem */
        $trashItem = $event->getTrashItem();
        if ($trashItem === null) {
            return;
        }

        $removedLocationContentIdMap = $trashItem->getRemovedLocationContentIdMap();

        foreach ($removedLocationContentIdMap as $contentId) {
            $spiScheduledEntries = $this->persistenceHandler
                ->getAllTypesEntries($contentId, null, 0, null);

            $this->persistenceHandler->deleteAllTypesEntries($contentId, null);

            foreach ($spiScheduledEntries as $spiScheduledEntry) {
                $this->createNotificationForTrash($spiScheduledEntry);
            }
        }
    }

    private function createNotificationForTrash(SPIScheduledEntry $scheduledVersion): void
    {
        $message = $this->translator->trans(
            /** @Desc("Content moved to Trash") */
            'content.trashed',
            [],
            'ibexa_scheduler'
        );

        $notification = $this->notificationFactory->getNotificationCreateStructBySPIEntry(
            $scheduledVersion,
            NotificationFactory::TYPE_UNSCHEDULED,
            true,
            $message
        );

        $this->notificationService->createNotification($notification);
    }

    private function createNotificationForDelete(SPIScheduledEntry $scheduledEntry): void
    {
        $message = $this->translator->trans(
            /** @Desc("Content deleted") */
            'content.deleted',
            [],
            'ibexa_scheduler'
        );

        $notification = $this->notificationFactory->getNotificationCreateStructBySPIEntry(
            $scheduledEntry,
            NotificationFactory::TYPE_UNSCHEDULED,
            false,
            $message
        );

        $notification->data['contentName'] = sprintf(
            '[id: %d, version: %d]',
            $scheduledEntry->contentId,
            $scheduledEntry->versionNumber ?? '-'
        );

        $this->notificationService->createNotification($notification);
    }
}

class_alias(RepositoryEventSubscriber::class, 'EzSystems\DateBasedPublisher\Core\Event\Subscriber\RepositoryEventSubscriber');
