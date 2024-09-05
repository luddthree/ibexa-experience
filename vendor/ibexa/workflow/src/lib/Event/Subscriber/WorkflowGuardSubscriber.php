<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Workflow\Event\Subscriber;

use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentCreateStruct;
use Ibexa\Contracts\Workflow\Registry\WorkflowRegistryInterface;
use Ibexa\Workflow\Value\WorkflowTransition;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\GuardEvent;

class WorkflowGuardSubscriber implements EventSubscriberInterface
{
    /** @var \Ibexa\Contracts\Core\Repository\PermissionResolver */
    private $permissionResolver;

    /** @var \Ibexa\Contracts\Workflow\Registry\WorkflowRegistryInterface */
    private $workflowRegistry;

    /**
     * @param \Ibexa\Contracts\Core\Repository\PermissionResolver $permissionResolver
     * @param \Ibexa\Contracts\Workflow\Registry\WorkflowRegistryInterface $workflowRegistry
     */
    public function __construct(
        PermissionResolver $permissionResolver,
        WorkflowRegistryInterface $workflowRegistry
    ) {
        $this->permissionResolver = $permissionResolver;
        $this->workflowRegistry = $workflowRegistry;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'workflow.guard' => ['onTransition', 0],
        ];
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\BadStateException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function onTransition(GuardEvent $event): void
    {
        $workflowName = $event->getWorkflowName();
        $subject = $event->getSubject();

        // guard is not needed for workflows not based on our content model
        if (
            (!$subject instanceof Content && !$subject instanceof ContentCreateStruct)
            || !$this->workflowRegistry->hasWorkflow($workflowName)
        ) {
            return;
        }

        $workflow = $this->workflowRegistry->getSupportedWorkflow(
            $workflowName,
            $subject
        );
        $workflowTransition = new WorkflowTransition([
            'workflow' => $workflow->getName(),
            'transition' => $event->getTransition()->getName(),
        ]);
        $permissionTargets = array_merge(
            [$workflowTransition],
            $subject instanceof ContentCreateStruct ? $subject->getLocationStructs() : []
        );

        $user = $this->permissionResolver->getCurrentUserReference();
        $marking = $workflow->getMarkingStore()->getMarking($subject);
        $context = $marking->getContext() ?? [];

        if (
            $this->permissionResolver->canUser('workflow', 'change_stage', $subject, $permissionTargets)
            && (
                !empty($context['reviewerId']) && $context['reviewerId'] == $user->getUserId()
                || empty($context['reviewerId'])
            )
        ) {
            return;
        }

        $event->setBlocked(true);
    }
}

class_alias(WorkflowGuardSubscriber::class, 'EzSystems\EzPlatformWorkflow\Event\Subscriber\WorkflowGuardSubscriber');
