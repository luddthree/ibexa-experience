<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Workflow\WorkflowTimeline\Value;

use Ibexa\Workflow\Value\WorkflowMetadata;

class WorkflowTimeline
{
    /** @var \Ibexa\Workflow\Value\WorkflowMetadata */
    private $workflowMetadata;

    /** @var \Ibexa\Workflow\WorkflowTimeline\Value\AbstractEntry[] */
    private $entries;

    /**
     * @param \Ibexa\Workflow\Value\WorkflowMetadata $workflowMetadata
     * @param \Ibexa\Workflow\WorkflowTimeline\Value\AbstractEntry[] $entries
     */
    public function __construct(
        WorkflowMetadata $workflowMetadata,
        array $entries = []
    ) {
        $this->workflowMetadata = $workflowMetadata;
        $this->entries = $entries;
    }

    /**
     * @return \Ibexa\Workflow\Value\WorkflowMetadata
     */
    public function getWorkflowMetadata(): WorkflowMetadata
    {
        return $this->workflowMetadata;
    }

    /**
     * @return \Ibexa\Workflow\WorkflowTimeline\Value\AbstractEntry[]
     */
    public function getEntries(): array
    {
        return $this->entries;
    }
}

class_alias(WorkflowTimeline::class, 'EzSystems\EzPlatformWorkflow\WorkflowTimeline\Value\WorkflowTimeline');
