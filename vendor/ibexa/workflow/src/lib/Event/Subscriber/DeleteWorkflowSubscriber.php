<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Workflow\Event\Subscriber;

use Ibexa\Contracts\Core\Repository\Events\Content\DeleteContentEvent;
use Ibexa\Contracts\Core\Repository\Events\Content\DeleteVersionEvent;
use Ibexa\Contracts\Core\Repository\Events\Trash\DeleteTrashItemEvent;
use Ibexa\Contracts\Core\Repository\Events\Trash\EmptyTrashEvent;
use Ibexa\Contracts\Workflow\Persistence\Handler\HandlerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class DeleteWorkflowSubscriber implements EventSubscriberInterface
{
    /** @var \Ibexa\Contracts\Workflow\Persistence\Handler\HandlerInterface */
    private $workflowHandler;

    /**
     * @param \Ibexa\Contracts\Workflow\Persistence\Handler\HandlerInterface $workflowHandler
     */
    public function __construct(HandlerInterface $workflowHandler)
    {
        $this->workflowHandler = $workflowHandler;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            DeleteVersionEvent::class => 'onDeleteVersion',
            DeleteContentEvent::class => 'onDeleteContent',
            DeleteTrashItemEvent::class => 'onDeleteTrashItem',
            EmptyTrashEvent::class => 'onEmptyTrash',
        ];
    }

    public function onDeleteVersion(DeleteVersionEvent $event): void
    {
        $versionInfo = $event->getVersionInfo();

        $this->doDeleteWorkflowMetadata($versionInfo->contentInfo->id, $versionInfo->versionNo);
    }

    public function onDeleteContent(DeleteContentEvent $event): void
    {
        $contentInfo = $event->getContentInfo();

        $this->doDeleteWorkflowMetadata($contentInfo->id);
    }

    public function onDeleteTrashItem(DeleteTrashItemEvent $event): void
    {
        $trashItemDeleteResult = $event->getResult();

        if (!$trashItemDeleteResult->contentRemoved) {
            return;
        }

        $this->doDeleteWorkflowMetadata($trashItemDeleteResult->contentId);
    }

    public function onEmptyTrash(EmptyTrashEvent $event): void
    {
        $resultList = $event->getResultList();

        foreach ($resultList as $trashItemDeleteResult) {
            if (!$trashItemDeleteResult->contentRemoved) {
                continue;
            }

            $this->doDeleteWorkflowMetadata($trashItemDeleteResult->contentId);
        }
    }

    private function doDeleteWorkflowMetadata(
        int $contentId,
        ?int $versionNo = null
    ): void {
        $persistenceWorkflowList = $this->workflowHandler->loadWorkflowMetadataByContent($contentId, $versionNo);
        foreach ($persistenceWorkflowList as $workflowMetadata) {
            $this->workflowHandler->deleteWorkflowMetadata($workflowMetadata->id);
        }
    }
}

class_alias(DeleteWorkflowSubscriber::class, 'EzSystems\EzPlatformWorkflow\Event\Subscriber\DeleteWorkflowSubscriber');
