<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Workflow\Value;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;

class WorkflowMarkingContext extends ValueObject
{
    /** @var int */
    public $workflowId;

    /** @var string */
    public $message;

    /** @var int */
    public $reviewerId;

    /** @var \Ibexa\Workflow\Value\WorkflowActionResult */
    public $result;
}

class_alias(WorkflowMarkingContext::class, 'EzSystems\EzPlatformWorkflow\Value\WorkflowMarkingContext');
