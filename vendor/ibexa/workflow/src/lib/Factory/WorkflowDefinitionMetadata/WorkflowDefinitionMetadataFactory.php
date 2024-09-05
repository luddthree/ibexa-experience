<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Workflow\Factory\WorkflowDefinitionMetadata;

use Ibexa\Contracts\Workflow\Factory\WorkflowDefinitionMetadata\WorkflowDefinitionMetadataFactoryInterface;
use Ibexa\Workflow\Value\WorkflowDefinitionMetadata;
use Ibexa\Workflow\Value\WorkflowMatcherDefinitionMetadata;
use Ibexa\Workflow\Value\WorkflowStageDefinitionMetadata;
use Ibexa\Workflow\Value\WorkflowTransitionDefinitionMetadata;
use Ibexa\Workflow\Value\WorkflowTransitionNotificationDefinitionMetadata;
use Ibexa\Workflow\Value\WorkflowTransitionReviewersDefinitionMetadata;

class WorkflowDefinitionMetadataFactory implements WorkflowDefinitionMetadataFactoryInterface
{
    /**
     * @param array $configuration
     *
     * @return \Ibexa\Workflow\Value\WorkflowDefinitionMetadata
     */
    public function createWorkflowDefinitionMetadata(array $configuration): WorkflowDefinitionMetadata
    {
        foreach ($configuration['stages'] as $stageIdentifier => $stageConfiguration) {
            $stagesDefinitionMetadata[$stageIdentifier] = $this->createWorkflowStageDefinitionMetadata($stageConfiguration);
        }

        foreach ($configuration['transitions'] as $transitionIdentifier => $transitionConfiguration) {
            $transitionsDefinitionMetadata[$transitionIdentifier] = $this->createWorkflowTransitionDefinitionMetadata($transitionConfiguration);
        }

        foreach ($configuration['matchers'] as $matcherIdentifier => $matcherConfiguration) {
            $matchersDefinitionMetadata[$matcherIdentifier] = $this->createWorkflowMatcherDefinitionMetadata($matcherConfiguration);
        }

        return new WorkflowDefinitionMetadata(
            $configuration['name'],
            $configuration['initial_stage'] ?? null,
            $stagesDefinitionMetadata ?? [],
            $transitionsDefinitionMetadata ?? [],
            $matchersDefinitionMetadata ?? []
        );
    }

    /**
     * @param array $configuration
     *
     * @return \Ibexa\Workflow\Value\WorkflowMatcherDefinitionMetadata
     */
    public function createWorkflowMatcherDefinitionMetadata(array $configuration): WorkflowMatcherDefinitionMetadata
    {
        return new WorkflowMatcherDefinitionMetadata($configuration);
    }

    /**
     * @param array $configuration
     *
     * @return \Ibexa\Workflow\Value\WorkflowTransitionDefinitionMetadata
     */
    public function createWorkflowTransitionDefinitionMetadata(array $configuration): WorkflowTransitionDefinitionMetadata
    {
        $reviewers = $this->createWorkflowTransitionReviewersDefinitionMetadata($configuration['reviewers']);
        $notification = $this->createWorkflowTransitionNotificationDefinitionMetadata($configuration['notification'] ?? []);

        return new WorkflowTransitionDefinitionMetadata(
            $notification,
            $reviewers,
            $configuration['label'],
            $configuration['validate'],
            $configuration['icon'],
            $configuration['color']
        );
    }

    /**
     * @param array $configuration
     *
     * @return \Ibexa\Workflow\Value\WorkflowStageDefinitionMetadata
     */
    public function createWorkflowStageDefinitionMetadata(array $configuration): WorkflowStageDefinitionMetadata
    {
        return new WorkflowStageDefinitionMetadata(
            $configuration['label'],
            $configuration['color'],
            $configuration['last_stage']
        );
    }

    /**
     * @param array $configuration
     *
     * @return \Ibexa\Workflow\Value\WorkflowTransitionNotificationDefinitionMetadata
     */
    public function createWorkflowTransitionNotificationDefinitionMetadata(array $configuration): WorkflowTransitionNotificationDefinitionMetadata
    {
        return new WorkflowTransitionNotificationDefinitionMetadata(
            $configuration['user_group'] ?? [],
            $configuration['user'] ?? []
        );
    }

    public function createWorkflowTransitionReviewersDefinitionMetadata(array $configuration): WorkflowTransitionReviewersDefinitionMetadata
    {
        return new WorkflowTransitionReviewersDefinitionMetadata(
            $configuration['required'],
            $configuration['user_group']
        );
    }
}

class_alias(WorkflowDefinitionMetadataFactory::class, 'EzSystems\EzPlatformWorkflow\Factory\WorkflowDefinitionMetadata\WorkflowDefinitionMetadataFactory');
