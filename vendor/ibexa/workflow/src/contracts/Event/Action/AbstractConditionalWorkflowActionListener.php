<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Workflow\Event\Action;

use Ibexa\Workflow\Value\WorkflowActionResult;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\Workflow\Event\Event;
use Symfony\Component\Workflow\Event\TransitionEvent;

abstract class AbstractConditionalWorkflowActionListener implements WorkflowActionListenerInterface, ConditionalActionListenerInterface
{
    /**
     * @param \Symfony\Component\Workflow\Event\TransitionEvent|\Symfony\Component\Workflow\Event\EnteredEvent $event
     */
    final public function onConditionalWorkflowEvent(Event $event): void
    {
        $subject = $event->getSubject();
        $marking = $event->getWorkflow()->getMarkingStore()->getMarking($subject);
        $metadata = $this->getActionMetadata(
            $event->getWorkflow(),
            $this instanceof AbstractTransitionWorkflowActionListener
                ? $event->getTransition()
                : $marking->getPlaces()
        );

        $expressionLanguage = new ExpressionLanguage();

        if (!empty($metadata['condition'])) {
            foreach ($metadata['condition'] as $condition) {
                $context = $marking->getContext() ?? [];
                $result = !empty($context['result']) && $context['result'] instanceof WorkflowActionResult
                    ? $context['result']->getResults()
                    : [];
                $parameters = [
                    'subject' => $event->getSubject(),
                    'workflow' => $event->getWorkflow(),
                    'context' => $context,
                    'result' => (object)$result,
                ];

                $expressionResult = $expressionLanguage->evaluate($condition, $parameters);

                if (!$expressionResult) {
                    return;
                }
            }
        }

        $this->onWorkflowEvent($event);
    }

    public function setResult(Event $event, $value): void
    {
        if (!$event instanceof TransitionEvent) {
            return;
        }

        $context = $event->getContext() ?? [];

        $context['result'] ??= new WorkflowActionResult();
        $context['result']->set($this->getIdentifier(), $value);

        $event->setContext($context);
    }
}

class_alias(AbstractConditionalWorkflowActionListener::class, 'EzSystems\EzPlatformWorkflow\Event\Action\AbstractConditionalWorkflowActionListener');
