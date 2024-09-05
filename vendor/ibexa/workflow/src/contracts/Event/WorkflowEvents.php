<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Workflow\Event;

class WorkflowEvents
{
    public const WORKFLOW_STAGE_CHANGE = 'ezplatform.workflow.stage_change';
}

class_alias(WorkflowEvents::class, 'EzSystems\EzPlatformWorkflow\Event\WorkflowEvents');
