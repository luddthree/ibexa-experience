<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Workflow\Value\Limitation;

use Ibexa\Contracts\Core\Repository\Values\User\Limitation;

class WorkflowStageLimitation extends Limitation
{
    public const IDENTIFIER = 'WorkflowStage';

    public function getIdentifier(): string
    {
        return self::IDENTIFIER;
    }
}

class_alias(WorkflowStageLimitation::class, 'EzSystems\EzPlatformWorkflow\Value\Limitation\WorkflowStageLimitation');
