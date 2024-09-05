<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Workflow\Event\Subscriber;

use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\Events\Content\CreateContentEvent;
use Ibexa\Contracts\Core\Repository\Events\Content\UpdateContentEvent;
use Ibexa\Contracts\Workflow\Registry\WorkflowRegistryInterface;
use Ibexa\Contracts\Workflow\Service\WorkflowServiceInterface;
use Ibexa\Workflow\Exception\NotFoundException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class StartWorkflowSubscriber implements EventSubscriberInterface
{
    /** @var \Ibexa\Contracts\Workflow\Service\WorkflowServiceInterface */
    private $workflowService;

    /** @var \Ibexa\Contracts\Workflow\Registry\WorkflowRegistryInterface */
    private $workflowRegistry;

    /** @var \Ibexa\Contracts\Core\Repository\ContentService */
    private $contentService;

    public function __construct(
        WorkflowServiceInterface $workflowService,
        WorkflowRegistryInterface $workflowRegistry,
        ContentService $contentService
    ) {
        $this->workflowService = $workflowService;
        $this->workflowRegistry = $workflowRegistry;
        $this->contentService = $contentService;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            UpdateContentEvent::class => 'onUpdateContent',
            CreateContentEvent::class => 'onCreateContent',
        ];
    }

    public function onUpdateContent(UpdateContentEvent $event): void
    {
        $content = $event->getContent();

        $this->doStartWorkflows((int)$content->id, (int)$content->versionInfo->versionNo);
    }

    public function onCreateContent(CreateContentEvent $event): void
    {
        $content = $event->getContent();

        $this->doStartWorkflows((int)$content->id, (int)$content->versionInfo->versionNo);
    }

    private function doStartWorkflows(
        int $contentId,
        int $versionNo
    ): void {
        $content = $this->contentService->loadContent($contentId, [], $versionNo);
        $supportedWorkflows = $this->workflowRegistry->getSupportedWorkflows($content);

        foreach ($supportedWorkflows as $workflow) {
            try {
                $this->workflowService->loadWorkflowMetadataForContent($content, $workflow->getName());
            } catch (NotFoundException $e) {
                $this->workflowService->start($content, $workflow->getName());
            }
        }
    }
}

class_alias(StartWorkflowSubscriber::class, 'EzSystems\EzPlatformWorkflow\Event\Subscriber\StartWorkflowSubscriber');
