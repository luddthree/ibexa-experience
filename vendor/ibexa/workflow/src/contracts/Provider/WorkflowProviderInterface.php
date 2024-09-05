<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Workflow\Provider;

interface WorkflowProviderInterface
{
    /**
     * @return \Symfony\Component\Workflow\Workflow[]
     */
    public function getWorkflows(): array;
}

class_alias(WorkflowProviderInterface::class, 'EzSystems\EzPlatformWorkflow\Provider\WorkflowProviderInterface');
