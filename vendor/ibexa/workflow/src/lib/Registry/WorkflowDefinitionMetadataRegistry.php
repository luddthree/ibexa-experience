<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Workflow\Registry;

use Ibexa\Contracts\Workflow\Registry\WorkflowDefinitionMetadataRegistryInterface;
use Ibexa\Workflow\Exception\NotFoundException;
use Ibexa\Workflow\Value\WorkflowDefinitionMetadata;

class WorkflowDefinitionMetadataRegistry implements WorkflowDefinitionMetadataRegistryInterface
{
    /** @var \Ibexa\Workflow\Value\WorkflowDefinitionMetadata[] */
    protected $metadata;

    /** @var \Ibexa\Contracts\Workflow\Factory\WorkflowDefinitionMetadata\WorkflowDefinitionMetadataFactoryInterface */
    protected $definitionMetadataFactory;

    /**
     * @param \Ibexa\Workflow\Value\WorkflowDefinitionMetadata[] $metadata
     */
    public function __construct(array $metadata = [])
    {
        $this->metadata = $metadata;
    }

    /**
     * @return \Ibexa\Workflow\Value\WorkflowDefinitionMetadata[]
     */
    public function getAllWorkflowMetadata(): array
    {
        return $this->metadata;
    }

    /**
     * @param \Ibexa\Workflow\Value\WorkflowDefinitionMetadata[] $allWorkflowMetadata
     */
    public function setAllWorkflowMetadata(array $allWorkflowMetadata): void
    {
        foreach ($allWorkflowMetadata as $identifier => $workflowMetadata) {
            $this->setWorkflowMetadata($identifier, $workflowMetadata);
        }
    }

    /**
     * @param string $identifier
     * @param \Ibexa\Workflow\Value\WorkflowDefinitionMetadata $workflowMetadata
     */
    public function setWorkflowMetadata(string $identifier, WorkflowDefinitionMetadata $workflowMetadata): void
    {
        $this->metadata[$identifier] = $workflowMetadata;
    }

    /**
     * @param string $identifier
     *
     * @return \Ibexa\Workflow\Value\WorkflowDefinitionMetadata
     *
     * @throws \Ibexa\Workflow\Exception\NotFoundException
     */
    public function getWorkflowMetadata(string $identifier): WorkflowDefinitionMetadata
    {
        if ($this->hasWorkflowMetadata($identifier)) {
            return $this->metadata[$identifier];
        }

        throw new NotFoundException('Workflow Metadata', $identifier);
    }

    /**
     * @param string $identifier
     *
     * @return bool
     */
    public function hasWorkflowMetadata(string $identifier): bool
    {
        return isset($this->metadata[$identifier]);
    }
}

class_alias(WorkflowDefinitionMetadataRegistry::class, 'EzSystems\EzPlatformWorkflow\Registry\WorkflowDefinitionMetadataRegistry');
