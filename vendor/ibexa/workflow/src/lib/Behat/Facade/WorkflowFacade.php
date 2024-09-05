<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Workflow\Behat\Facade;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Workflow\Registry\WorkflowRegistryInterface;
use Ibexa\Contracts\Workflow\Service\WorkflowServiceInterface;

class WorkflowFacade
{
    /**
     * @var \Ibexa\Contracts\Workflow\Service\WorkflowServiceInterface
     */
    private $workflowService;

    /**
     * @var \Ibexa\Contracts\Workflow\Registry\WorkflowRegistryInterface
     */
    private $workflowRegistry;

    public function __construct(WorkflowServiceInterface $workflowService, WorkflowRegistryInterface $workflowRegistry)
    {
        $this->workflowService = $workflowService;
        $this->workflowRegistry = $workflowRegistry;
    }

    public function transition(Content $content, string $transitionName, string $transitionMessage)
    {
        $availableWorkflow = $this->workflowRegistry->getSupportedWorkflows($content);

        foreach ($availableWorkflow as $workflow) {
            // We want to use userdefined Workflows, not Quick Review
            if ($workflow->getName() !== 'quick_review') {
                $workflowName = $workflow->getName();
                break;
            }
        }

        try {
            $workflowMetadata = $this->workflowService->loadWorkflowMetadataForContent($content, $workflowName);
        } catch (NotFoundException $e) {
            $workflowMetadata = $this->workflowService->start($content, $workflowName);
        }

        $workflow = $workflowMetadata->workflow;

        $workflow->apply($workflowMetadata->content, $transitionName, [
            'message' => $transitionMessage,
            'reviewerId' => null,
        ]);
    }
}
