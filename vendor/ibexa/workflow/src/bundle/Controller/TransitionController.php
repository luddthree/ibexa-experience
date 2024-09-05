<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Workflow\Controller;

use Ibexa\Contracts\AdminUi\Controller\Controller;
use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Workflow\Registry\WorkflowDefinitionMetadataRegistryInterface;
use Ibexa\Contracts\Workflow\Service\WorkflowServiceInterface;
use Ibexa\Workflow\WorkflowTimeline\WorkflowTimelineFactory;
use Symfony\Component\HttpFoundation\Response;

class TransitionController extends Controller
{
    /** @var \Ibexa\Contracts\Core\Repository\ContentService */
    private $contentService;

    /** @var \Ibexa\Contracts\Workflow\Service\WorkflowServiceInterface */
    private $workflowService;

    /** @var \Ibexa\Contracts\Workflow\Registry\WorkflowDefinitionMetadataRegistryInterface */
    private $workflowDefinitionMetadataRegistry;

    /** @var \Ibexa\Workflow\WorkflowTimeline\WorkflowTimelineFactory */
    private $workflowTimelineFactory;

    /**
     * @param \Ibexa\Contracts\Core\Repository\ContentService $contentService
     * @param \Ibexa\Contracts\Workflow\Service\WorkflowServiceInterface $workflowService
     * @param \Ibexa\Contracts\Workflow\Registry\WorkflowDefinitionMetadataRegistryInterface $workflowDefinitionMetadataRegistry
     * @param \Ibexa\Workflow\WorkflowTimeline\WorkflowTimelineFactory $workflowTimelineEntriesCollector
     */
    public function __construct(
        ContentService $contentService,
        WorkflowServiceInterface $workflowService,
        WorkflowDefinitionMetadataRegistryInterface $workflowDefinitionMetadataRegistry,
        WorkflowTimelineFactory $workflowTimelineEntriesCollector
    ) {
        $this->contentService = $contentService;
        $this->workflowService = $workflowService;
        $this->workflowDefinitionMetadataRegistry = $workflowDefinitionMetadataRegistry;
        $this->workflowTimelineFactory = $workflowTimelineEntriesCollector;
    }

    /**
     * @param int $contentId
     * @param int $versionNo
     * @param string $workflowName
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Ibexa\Workflow\Exception\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function getTransitionListSnippetAction(int $contentId, int $versionNo, string $workflowName): Response
    {
        $content = $this->contentService->loadContent($contentId, null, $versionNo);
        $workflowMetadata = $this->workflowService->loadWorkflowMetadataForContent($content, $workflowName);
        $workflowDefinitionMetadata = $this->workflowDefinitionMetadataRegistry->getWorkflowMetadata($workflowName);
        $timeline = $this->workflowTimelineFactory->create($workflowMetadata);

        return $this->render('@ibexadesign/ibexa_workflow/timeline/list.html.twig', [
            'workflow_metadata' => $workflowMetadata,
            'workflow_definition_metadata' => $workflowDefinitionMetadata,
            'timeline' => $timeline,
        ]);
    }
}

class_alias(TransitionController::class, 'EzSystems\EzPlatformWorkflowBundle\Controller\TransitionController');
