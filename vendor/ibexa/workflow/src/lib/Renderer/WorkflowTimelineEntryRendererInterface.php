<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Workflow\Renderer;

use Ibexa\Workflow\Value\WorkflowMetadata;
use Ibexa\Workflow\WorkflowTimeline\Value\AbstractEntry;

interface WorkflowTimelineEntryRendererInterface
{
    /**
     * @param \Ibexa\Workflow\Value\WorkflowMetadata $workflowMetadata
     * @param \Ibexa\Workflow\WorkflowTimeline\Value\AbstractEntry $entry
     * @param array $parameters
     *
     * @return string
     */
    public function render(
        WorkflowMetadata $workflowMetadata,
        AbstractEntry $entry,
        array $parameters = []
    ): string;
}

class_alias(WorkflowTimelineEntryRendererInterface::class, 'EzSystems\EzPlatformWorkflow\Renderer\WorkflowTimelineEntryRendererInterface');
