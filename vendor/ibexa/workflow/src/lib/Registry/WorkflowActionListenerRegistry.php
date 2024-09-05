<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Workflow\Registry;

use Ibexa\Contracts\Workflow\Event\Action\WorkflowActionListenerInterface;
use Ibexa\Contracts\Workflow\Registry\WorkflowActionListenerRegistryInterface;

class WorkflowActionListenerRegistry implements WorkflowActionListenerRegistryInterface
{
    /** @var \Ibexa\Contracts\Workflow\Event\Action\WorkflowActionListenerInterface[] */
    private $actionListeners;

    /**
     * @param \Ibexa\Contracts\Workflow\Event\Action\WorkflowActionListenerInterface[] $actionListeners
     */
    public function __construct(iterable $actionListeners = [])
    {
        foreach ($actionListeners as $actionListener) {
            $this->addActionListener($actionListener);
        }
    }

    public function getActionListener(string $identifier): ?WorkflowActionListenerInterface
    {
        return $this->actionListeners[$identifier] ?? null;
    }

    public function addActionListener(WorkflowActionListenerInterface $actionListener): void
    {
        $this->actionListeners[$actionListener->getIdentifier()] = $actionListener;
    }
}

class_alias(WorkflowActionListenerRegistry::class, 'EzSystems\EzPlatformWorkflow\Registry\WorkflowActionListenerRegistry');
