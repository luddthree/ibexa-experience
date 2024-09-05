<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Workflow\Builder;

use Ibexa\Contracts\Workflow\Registry\WorkflowDefinitionMetadataRegistryInterface;

class StagesMapBuilder
{
    /** @var \Ibexa\Contracts\Workflow\Registry\WorkflowDefinitionMetadataRegistryInterface */
    private $workflowDefinitionMetadataRegistry;

    /**
     * @param \Ibexa\Contracts\Workflow\Registry\WorkflowDefinitionMetadataRegistryInterface $workflowDefinitionMetadataRegistry
     */
    public function __construct(
        WorkflowDefinitionMetadataRegistryInterface $workflowDefinitionMetadataRegistry
    ) {
        $this->workflowDefinitionMetadataRegistry = $workflowDefinitionMetadataRegistry;
    }

    /**
     * @param \Ibexa\Workflow\Value\WorkflowMetadata[] $workflowsMetadata
     *
     * @return array
     */
    public function buildStagesMapByWorkflow(array $workflowsMetadata): array
    {
        $stagesMap = [];

        foreach ($workflowsMetadata as $workflowMetadata) {
            $workflowName = $workflowMetadata->name;
            $workflowDefinitionMetadata = $this->workflowDefinitionMetadataRegistry->getWorkflowMetadata($workflowName);

            $contentId = $workflowMetadata->versionInfo->getContentInfo()->id;
            if (!array_key_exists($contentId, $stagesMap)) {
                $stagesMap[$contentId] = [];
            }

            $versionNo = $workflowMetadata->versionInfo->versionNo;
            if (!array_key_exists($versionNo, $stagesMap[$contentId])) {
                $stagesMap[$contentId][$versionNo] = [];
            }

            $stagesMap[$contentId][$versionNo][$workflowName] = [];

            foreach ($workflowMetadata->markings as $markingMetadata) {
                $stagesMap[$contentId][$versionNo][$workflowName][$markingMetadata->name] = [
                    'color' => $workflowDefinitionMetadata->getStagesMetadata()[$markingMetadata->name]->getColor(),
                    'label' => $workflowDefinitionMetadata->getStagesMetadata()[$markingMetadata->name]->getLabel(),
                ];
            }
        }

        return $stagesMap;
    }
}

class_alias(StagesMapBuilder::class, 'EzSystems\EzPlatformWorkflow\Builder\StagesMapBuilder');
