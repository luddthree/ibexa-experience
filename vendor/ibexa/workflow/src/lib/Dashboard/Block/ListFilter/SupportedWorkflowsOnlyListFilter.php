<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Workflow\Dashboard\Block\ListFilter;

use Ibexa\Workflow\Value\WorkflowMetadata;
use Symfony\Component\Workflow\SupportStrategy\WorkflowSupportStrategyInterface;

class SupportedWorkflowsOnlyListFilter implements ListFilterInterface
{
    /** @var \Symfony\Component\Workflow\SupportStrategy\WorkflowSupportStrategyInterface */
    private $supportStrategy;

    /**
     * @param \Symfony\Component\Workflow\SupportStrategy\WorkflowSupportStrategyInterface $supportStrategy
     */
    public function __construct(WorkflowSupportStrategyInterface $supportStrategy)
    {
        $this->supportStrategy = $supportStrategy;
    }

    /**
     * {@inheritdoc}
     */
    public function filter(array $workflowMetadataList): array
    {
        return array_filter($workflowMetadataList, function (WorkflowMetadata $workflowMetadata): bool {
            return $this->supportStrategy->supports($workflowMetadata->workflow, $workflowMetadata->versionInfo);
        });
    }
}

class_alias(SupportedWorkflowsOnlyListFilter::class, 'EzSystems\EzPlatformWorkflow\Dashboard\Block\ListFilter\SupportedWorkflowsOnlyListFilter');
