<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Workflow\Dashboard\Block\ListFilter;

use Ibexa\Contracts\Workflow\Registry\WorkflowDefinitionMetadataRegistryInterface;

class IgnoreContentOnLastStageListFilter implements ListFilterInterface
{
    /** @var \Ibexa\Contracts\Workflow\Registry\WorkflowDefinitionMetadataRegistryInterface */
    private $workflowDefinitionMetadataRegistry;

    /**
     * @param \Ibexa\Contracts\Workflow\Registry\WorkflowDefinitionMetadataRegistryInterface $workflowDefinitionMetadataRegistry
     */
    public function __construct(WorkflowDefinitionMetadataRegistryInterface $workflowDefinitionMetadataRegistry)
    {
        $this->workflowDefinitionMetadataRegistry = $workflowDefinitionMetadataRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function filter(array $workflowMetadataList): array
    {
        $filteredList = [];
        /** @var \Ibexa\Workflow\Value\WorkflowMetadata $workflowMetadata */
        foreach ($workflowMetadataList as $workflowMetadata) {
            $definitionMetadata = $this->workflowDefinitionMetadataRegistry->getWorkflowMetadata($workflowMetadata->name);

            $notLastMarkings = [];
            foreach ($workflowMetadata->markings as $markingMetadata) {
                $stageDefinition = $definitionMetadata->getStageMetadata($markingMetadata->name);

                if ($stageDefinition->isLastStage()) {
                    continue;
                }

                $notLastMarkings = $markingMetadata;
            }

            if (!empty($notLastMarkings)) {
                $filteredList[] = $workflowMetadata;
            }
        }

        return $filteredList;
    }
}

class_alias(IgnoreContentOnLastStageListFilter::class, 'EzSystems\EzPlatformWorkflow\Dashboard\Block\ListFilter\IgnoreContentOnLastStageListFilter');
