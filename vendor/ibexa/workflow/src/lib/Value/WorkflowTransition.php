<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Workflow\Value;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;

class WorkflowTransition extends ValueObject
{
    /** @var string */
    public $workflow;

    /** @var string */
    public $transition;
}

class_alias(WorkflowTransition::class, 'EzSystems\EzPlatformWorkflow\Value\WorkflowTransition');
