<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Workflow\Event\Action;

use Symfony\Component\Workflow\Event\TransitionEvent;
use Symfony\Component\Workflow\Transition;
use Symfony\Component\Workflow\WorkflowInterface;

abstract class AbstractTransitionWorkflowActionListener extends AbstractConditionalWorkflowActionListener
{
    public function getActionMetadata(WorkflowInterface $workflow, Transition $transition): ?array
    {
        return $workflow->getMetadataStore()->getTransitionMetadata($transition)['actions'][$this->getIdentifier()] ?? null;
    }

    abstract public function onWorkflowEvent(TransitionEvent $event): void;
}

class_alias(AbstractTransitionWorkflowActionListener::class, 'EzSystems\EzPlatformWorkflow\Event\Action\AbstractTransitionWorkflowActionListener');
