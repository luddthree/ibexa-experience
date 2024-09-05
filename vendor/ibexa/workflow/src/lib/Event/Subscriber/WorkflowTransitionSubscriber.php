<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Workflow\Event\Subscriber;

use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Workflow\Persistence\Handler\HandlerInterface;
use Ibexa\Workflow\Registry\WorkflowRegistry;
use Ibexa\Workflow\Value\Persistence\TransitionMetadataCreateStruct;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\EnteredEvent;

class WorkflowTransitionSubscriber implements EventSubscriberInterface
{
    /** @var \Ibexa\Contracts\Core\Repository\PermissionResolver */
    private $permissionResolver;

    /** @var \Ibexa\Contracts\Workflow\Persistence\Handler\HandlerInterface */
    private $workflowHandler;

    private WorkflowRegistry $workflowRegistry;

    public function __construct(
        PermissionResolver $permissionResolver,
        HandlerInterface $workflowHandler,
        WorkflowRegistry $workflowRegistry
    ) {
        $this->permissionResolver = $permissionResolver;
        $this->workflowHandler = $workflowHandler;
        $this->workflowRegistry = $workflowRegistry;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'workflow.entered' => ['onWorkflowEntered', -255],
        ];
    }

    public function onWorkflowEntered(EnteredEvent $event): void
    {
        if (!$this->workflowRegistry->hasWorkflow($event->getWorkflow()->getName())) {
            return;
        }

        $marking = $event->getWorkflow()->getMarkingStore()->getMarking($event->getSubject());

        if (!$event->getTransition()) {
            return;
        }

        $context = $marking->getContext() ?? [];

        $createStruct = new TransitionMetadataCreateStruct();
        $createStruct->name = $event->getTransition()->getName();
        $createStruct->message = $context['message'];
        $createStruct->userId = $this->permissionResolver->getCurrentUserReference()->getUserId();
        $createStruct->date = time();

        $this->workflowHandler->createTransitionMetadata($createStruct, $context['workflowId']);
    }
}

class_alias(WorkflowTransitionSubscriber::class, 'EzSystems\EzPlatformWorkflow\Event\Subscriber\WorkflowTransitionSubscriber');
