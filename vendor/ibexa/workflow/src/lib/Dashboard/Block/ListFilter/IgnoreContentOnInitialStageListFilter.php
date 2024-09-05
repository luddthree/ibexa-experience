<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Workflow\Dashboard\Block\ListFilter;

use Ibexa\Contracts\Workflow\Registry\WorkflowDefinitionMetadataRegistryInterface;

class IgnoreContentOnInitialStageListFilter implements ListFilterInterface
{
    /** @var \Ibexa\Contracts\Workflow\Registry\WorkflowDefinitionMetadataRegistryInterface */
    private $workflowDefinitionMetadataRegistry;

    public function __construct(WorkflowDefinitionMetadataRegistryInterface $workflowDefinitionMetadataRegistry)
    {
        $this->workflowDefinitionMetadataRegistry = $workflowDefinitionMetadataRegistry;
    }

    public function filter(array $workflowMetadataList): array
    {
        $filteredList = [];
        /** @var \Ibexa\Workflow\Value\WorkflowMetadata $workflowMetadata */
        foreach ($workflowMetadataList as $workflowMetadata) {
            $initialStages = $workflowMetadata->workflow->getDefinition()->getInitialPlaces();

            $notInitialMarkings = [];

            foreach ($workflowMetadata->markings as $markingMetadata) {
                if (empty($workflowMetadata->transitions) && in_array($markingMetadata->name, $initialStages, true)) {
                    continue;
                }

                $notInitialMarkings = $markingMetadata;
            }

            if (!empty($notInitialMarkings)) {
                $filteredList[] = $workflowMetadata;
            }
        }

        return $filteredList;
    }
}

class_alias(IgnoreContentOnInitialStageListFilter::class, 'EzSystems\EzPlatformWorkflow\Dashboard\Block\ListFilter\IgnoreContentOnInitialStageListFilter');
