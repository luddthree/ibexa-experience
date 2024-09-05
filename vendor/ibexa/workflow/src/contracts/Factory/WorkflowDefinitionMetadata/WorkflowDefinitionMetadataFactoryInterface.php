<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Workflow\Factory\WorkflowDefinitionMetadata;

use Ibexa\Workflow\Value\WorkflowDefinitionMetadata;
use Ibexa\Workflow\Value\WorkflowMatcherDefinitionMetadata;
use Ibexa\Workflow\Value\WorkflowStageDefinitionMetadata;
use Ibexa\Workflow\Value\WorkflowTransitionDefinitionMetadata;
use Ibexa\Workflow\Value\WorkflowTransitionNotificationDefinitionMetadata;

interface WorkflowDefinitionMetadataFactoryInterface
{
    /**
     * @param array $configuration
     *
     * @return \Ibexa\Workflow\Value\WorkflowDefinitionMetadata
     */
    public function createWorkflowDefinitionMetadata(array $configuration): WorkflowDefinitionMetadata;

    /**
     * @param array $configuration
     *
     * @return \Ibexa\Workflow\Value\WorkflowMatcherDefinitionMetadata
     */
    public function createWorkflowMatcherDefinitionMetadata(array $configuration): WorkflowMatcherDefinitionMetadata;

    /**
     * @param array $configuration
     *
     * @return \Ibexa\Workflow\Value\WorkflowTransitionDefinitionMetadata
     */
    public function createWorkflowTransitionDefinitionMetadata(array $configuration): WorkflowTransitionDefinitionMetadata;

    /**
     * @param array $configuration
     *
     * @return \Ibexa\Workflow\Value\WorkflowStageDefinitionMetadata
     */
    public function createWorkflowStageDefinitionMetadata(array $configuration): WorkflowStageDefinitionMetadata;

    /**
     * @param array $configuration
     *
     * @return \Ibexa\Workflow\Value\WorkflowTransitionNotificationDefinitionMetadata
     */
    public function createWorkflowTransitionNotificationDefinitionMetadata(array $configuration): WorkflowTransitionNotificationDefinitionMetadata;
}

class_alias(WorkflowDefinitionMetadataFactoryInterface::class, 'EzSystems\EzPlatformWorkflow\Factory\WorkflowDefinitionMetadata\WorkflowDefinitionMetadataFactoryInterface');
