<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Workflow\Component;

use Ibexa\AdminUi\Component\TwigComponent;
use Ibexa\Contracts\Workflow\Registry\WorkflowDefinitionMetadataRegistryInterface;
use Ibexa\Contracts\Workflow\Registry\WorkflowRegistryInterface;
use Ibexa\Contracts\Workflow\Service\WorkflowServiceInterface;
use Ibexa\Workflow\Exception\NotFoundException;
use Ibexa\Workflow\WorkflowTimeline\WorkflowTimelineFactory;
use Twig\Environment;

class WorkflowTimelineComponent extends TwigComponent
{
    /** @var \Ibexa\Contracts\Workflow\Service\WorkflowServiceInterface */
    private $workflowService;

    /** @var \Ibexa\Contracts\Workflow\Registry\WorkflowRegistryInterface */
    private $workflowRegistry;

    /** @var \Ibexa\Contracts\Workflow\Registry\WorkflowDefinitionMetadataRegistryInterface */
    private $workflowDefinitionMetadataRegistry;

    /** @var \Ibexa\Workflow\WorkflowTimeline\WorkflowTimelineFactory */
    private $timelineFactory;

    /**
     * @param \Twig\Environment $twig
     * @param \Ibexa\Contracts\Workflow\Service\WorkflowServiceInterface $workflowService
     * @param \Ibexa\Contracts\Workflow\Registry\WorkflowRegistryInterface $workflowRegistry
     * @param \Ibexa\Contracts\Workflow\Registry\WorkflowDefinitionMetadataRegistryInterface $workflowDefinitionMetadataRegistry
     * @param \Ibexa\Workflow\WorkflowTimeline\WorkflowTimelineFactory $timelineFactory
     * @param array $parameters
     */
    public function __construct(
        Environment $twig,
        WorkflowServiceInterface $workflowService,
        WorkflowRegistryInterface $workflowRegistry,
        WorkflowDefinitionMetadataRegistryInterface $workflowDefinitionMetadataRegistry,
        WorkflowTimelineFactory $timelineFactory,
        array $parameters = []
    ) {
        parent::__construct(
            $twig,
            '@ibexadesign/ibexa_workflow/admin/component/workflow_timeline.html.twig',
            $parameters
        );

        $this->workflowService = $workflowService;
        $this->workflowRegistry = $workflowRegistry;
        $this->workflowDefinitionMetadataRegistry = $workflowDefinitionMetadataRegistry;
        $this->timelineFactory = $timelineFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function render(array $parameters = []): string
    {
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Content $content */
        $content = $parameters['content'];

        $workflowParameters = [];
        foreach ($this->workflowRegistry->getSupportedWorkflows($content) as $workflow) {
            $workflowName = $workflow->getName();
            try {
                $workflowMetadata = $this->workflowService->loadWorkflowMetadataForContent(
                    $content,
                    $workflowName
                );
            } catch (NotFoundException $e) {
                continue;
            }

            $workflowDefinitionMetadata = $this->workflowDefinitionMetadataRegistry->getWorkflowMetadata($workflowName);

            $workflowParameters[$workflowName] = [
                'workflow_metadata' => $workflowMetadata,
                'workflow_definition_metadata' => $workflowDefinitionMetadata,
                'transition_metadata_list' => $workflowMetadata->transitions,
                'timeline' => $this->timelineFactory->create($workflowMetadata),
            ];
        }

        $parameters = array_replace($parameters, ['workflows' => $workflowParameters]);

        return parent::render($parameters);
    }
}

class_alias(WorkflowTimelineComponent::class, 'EzSystems\EzPlatformWorkflow\Component\WorkflowTimelineComponent');
