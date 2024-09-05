<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ActivityLog\Event;

use Ibexa\Contracts\ActivityLog\ActivityLogServiceInterface;
use Ibexa\Contracts\AdminUi\Autosave\AutosaveServiceInterface;
use Ibexa\Contracts\Core\Repository\Events\Content\CreateContentDraftEvent;
use Ibexa\Contracts\Core\Repository\Events\Content\CreateContentEvent;
use Ibexa\Contracts\Core\Repository\Events\Content\DeleteContentEvent;
use Ibexa\Contracts\Core\Repository\Events\Content\DeleteTranslationEvent;
use Ibexa\Contracts\Core\Repository\Events\Content\HideContentEvent;
use Ibexa\Contracts\Core\Repository\Events\Content\PublishVersionEvent;
use Ibexa\Contracts\Core\Repository\Events\Content\RevealContentEvent;
use Ibexa\Contracts\Core\Repository\Events\Content\UpdateContentEvent;
use Ibexa\Contracts\Core\Repository\Events\Trash\RecoverEvent;
use Ibexa\Contracts\Core\Repository\Events\Trash\TrashEvent;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class ContentEventsListener implements EventSubscriberInterface
{
    private ActivityLogServiceInterface $activityLogService;

    private ?AutosaveServiceInterface $autosaveService;

    public function __construct(
        ActivityLogServiceInterface $activityLogService,
        ?AutosaveServiceInterface $autosaveService
    ) {
        $this->activityLogService = $activityLogService;
        $this->autosaveService = $autosaveService;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            CreateContentDraftEvent::class => ['onCreateContentDraft'],
            CreateContentEvent::class => ['onCreateContent'],
            UpdateContentEvent::class => ['onUpdateContent'],
            TrashEvent::class => ['onTrashContent'],
            RecoverEvent::class => ['onRecoverContent'],
            DeleteContentEvent::class => ['onDeleteContent'],
            DeleteTranslationEvent::class => ['onDeleteContentTranslation'],
            HideContentEvent::class => ['onHideContent'],
            RevealContentEvent::class => ['onRevealContent'],
            PublishVersionEvent::class => ['onPublishContent'],
        ];
    }

    public function onCreateContentDraft(CreateContentDraftEvent $event): void
    {
        $content = $event->getContentDraft();
        $this->saveActivityLog(ActivityLogServiceInterface::ACTION_CREATE_DRAFT, $event->getContentInfo()->getId(), $content->getName());
    }

    public function onCreateContent(CreateContentEvent $event): void
    {
        $content = $event->getContent();
        $this->saveActivityLog(ActivityLogServiceInterface::ACTION_CREATE, $content->id, $content->getName());
    }

    public function onUpdateContent(UpdateContentEvent $event): void
    {
        if ($this->autosaveService !== null && $this->autosaveService->isInProgress()) {
            // Skip autosaved drafts
            return;
        }

        $content = $event->getContent();
        $this->saveActivityLog(ActivityLogServiceInterface::ACTION_UPDATE, $content->id, $content->getName());
    }

    public function onPublishContent(PublishVersionEvent $event): void
    {
        $content = $event->getContent();
        $this->saveActivityLog(ActivityLogServiceInterface::ACTION_PUBLISH, $content->id, $content->getName());
    }

    public function onRecoverContent(RecoverEvent $event): void
    {
        $contentInfo = $event->getTrashItem()->getContentInfo();
        $this->saveActivityLog(ActivityLogServiceInterface::ACTION_RESTORE, $contentInfo->getId(), $contentInfo->name);
    }

    public function onTrashContent(TrashEvent $event): void
    {
        $item = $event->getTrashItem();
        if ($item === null) {
            return;
        }

        $contentInfo = $item->getContentInfo();
        $this->saveActivityLog(ActivityLogServiceInterface::ACTION_TRASH, $contentInfo->getId(), $contentInfo->name);
    }

    public function onDeleteContent(DeleteContentEvent $event): void
    {
        $contentInfo = $event->getContentInfo();
        $this->saveActivityLog(ActivityLogServiceInterface::ACTION_DELETE, $contentInfo->getId(), $contentInfo->name);
    }

    public function onDeleteContentTranslation(DeleteTranslationEvent $event): void
    {
        $contentInfo = $event->getContentInfo();
        $this->saveActivityLog(ActivityLogServiceInterface::ACTION_DELETE_TRANSLATION, $contentInfo->getId(), $contentInfo->name);
    }

    public function onHideContent(HideContentEvent $event): void
    {
        $contentInfo = $event->getContentInfo();
        $this->saveActivityLog(ActivityLogServiceInterface::ACTION_HIDE, $contentInfo->getId(), $contentInfo->name);
    }

    public function onRevealContent(RevealContentEvent $event): void
    {
        $contentInfo = $event->getContentInfo();
        $this->saveActivityLog(ActivityLogServiceInterface::ACTION_REVEAL, $contentInfo->getId(), $contentInfo->name);
    }

    private function saveActivityLog(string $action, int $id, ?string $name): void
    {
        $activityLog = $this->activityLogService->build(Content::class, (string)$id, $action);
        if ($name !== null) {
            $activityLog->setObjectName($name);
        }
        $this->activityLogService->save($activityLog);
    }
}
