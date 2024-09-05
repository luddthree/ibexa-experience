<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Workflow\Registry;

use Ibexa\Contracts\Workflow\Event\Action\WorkflowActionListenerInterface;

interface WorkflowActionListenerRegistryInterface
{
    public function getActionListener(string $identifier): ?WorkflowActionListenerInterface;

    public function addActionListener(WorkflowActionListenerInterface $actionListener): void;
}

class_alias(WorkflowActionListenerRegistryInterface::class, 'EzSystems\EzPlatformWorkflow\Registry\WorkflowActionListenerRegistryInterface');
