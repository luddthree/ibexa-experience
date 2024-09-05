<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Workflow\Registry;

use Ibexa\Workflow\Value\WorkflowDefinitionMetadata;

interface WorkflowDefinitionMetadataRegistryInterface
{
    /**
     * @return \Ibexa\Workflow\Value\WorkflowDefinitionMetadata[]
     */
    public function getAllWorkflowMetadata(): array;

    /**
     * @param \Ibexa\Workflow\Value\WorkflowDefinitionMetadata[] $allWorkflowMetadata
     */
    public function setAllWorkflowMetadata(array $allWorkflowMetadata): void;

    /**
     * @param string $identifier
     * @param \Ibexa\Workflow\Value\WorkflowDefinitionMetadata $workflowMetadata
     */
    public function setWorkflowMetadata(string $identifier, WorkflowDefinitionMetadata $workflowMetadata): void;

    /**
     * @param string $identifier
     *
     * @return \Ibexa\Workflow\Value\WorkflowDefinitionMetadata
     */
    public function getWorkflowMetadata(string $identifier): WorkflowDefinitionMetadata;

    /**
     * @param string $identifier
     *
     * @return bool
     */
    public function hasWorkflowMetadata(string $identifier): bool;
}

class_alias(WorkflowDefinitionMetadataRegistryInterface::class, 'EzSystems\EzPlatformWorkflow\Registry\WorkflowDefinitionMetadataRegistryInterface');
