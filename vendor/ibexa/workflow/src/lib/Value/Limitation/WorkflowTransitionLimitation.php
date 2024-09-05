<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Workflow\Value\Limitation;

use Ibexa\Contracts\Core\Repository\Values\User\Limitation;

class WorkflowTransitionLimitation extends Limitation
{
    public const IDENTIFIER = 'WorkflowTransition';

    /**
     * {@inheritdoc}
     */
    public function getIdentifier(): string
    {
        return self::IDENTIFIER;
    }
}

class_alias(WorkflowTransitionLimitation::class, 'EzSystems\EzPlatformWorkflow\Value\Limitation\WorkflowTransitionLimitation');
