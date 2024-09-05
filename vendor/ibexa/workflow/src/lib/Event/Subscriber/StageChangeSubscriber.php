<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Workflow\Event\Subscriber;

use Ibexa\Contracts\Core\Repository\NotificationService;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\Core\Repository\Values\Notification\CreateStruct;
use Ibexa\Contracts\Workflow\Event\StageChangeEvent;
use Ibexa\Contracts\Workflow\Event\WorkflowEvents;
use Ibexa\Workflow\Registry\WorkflowDefinitionMetadataRegistry;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class StageChangeSubscriber implements EventSubscriberInterface
{
    /** @var \Ibexa\Contracts\Core\Repository\PermissionResolver */
    private $permissionResolver;

    /** @var \Ibexa\Workflow\Registry\WorkflowDefinitionMetadataRegistry */
    private $workflowMetadataRegistry;

    /** @var \Ibexa\Contracts\Core\Repository\UserService */
    private $userService;

    /** @var \Ibexa\Contracts\Core\Repository\Repository */
    private $repository;

    /** @var \Ibexa\Contracts\Core\Repository\NotificationService */
    private $notificationService;

    /**
     * @param \Ibexa\Contracts\Core\Repository\NotificationService $notificationService
     * @param \Ibexa\Contracts\Core\Repository\PermissionResolver $permissionResolver
     * @param \Ibexa\Contracts\Core\Repository\Repository $repository
     * @param \Ibexa\Contracts\Core\Repository\UserService $userService
     * @param \Ibexa\Workflow\Registry\WorkflowDefinitionMetadataRegistry $workflowMetadataRegistry
     */
    public function __construct(
        NotificationService $notificationService,
        PermissionResolver $permissionResolver,
        Repository $repository,
        UserService $userService,
        WorkflowDefinitionMetadataRegistry $workflowMetadataRegistry
    ) {
        $this->permissionResolver = $permissionResolver;
        $this->workflowMetadataRegistry = $workflowMetadataRegistry;
        $this->userService = $userService;
        $this->notificationService = $notificationService;
        $this->repository = $repository;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            WorkflowEvents::WORKFLOW_STAGE_CHANGE => ['onStageChange', 0],
        ];
    }

    /**
     * @param \Ibexa\Contracts\Workflow\Event\StageChangeEvent $event
     *
     * @throws \Ibexa\Workflow\Exception\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function onStageChange(StageChangeEvent $event): void
    {
        $workflowName = $event->getWorkflowMetadata()->name;

        if (!$this->workflowMetadataRegistry->hasWorkflowMetadata($workflowName)) {
            return;
        }

        $workflowDefinitionMetadata = $this->workflowMetadataRegistry->getWorkflowMetadata($workflowName);
        $transitionMetadata = $workflowDefinitionMetadata->getTransitionMetadata($event->getTransitionMetadata()->name);
        $notificationMetadata = $transitionMetadata->getNotificationMetadata();

        $users = [];

        $this->permissionResolver->sudo(function () use (&$users, $notificationMetadata) {
            foreach ($notificationMetadata->getUserGroups() as $userGroupId) {
                $groupUsers = $this->userService->loadUsersOfUserGroup($this->userService->loadUserGroup($userGroupId));

                foreach ($groupUsers as $groupUser) {
                    $users[$groupUser->id] = $users[$groupUser->id] ?? $groupUser;
                }
            }

            foreach ($notificationMetadata->getUsers() as $userId) {
                $users[$userId] = $users[$userId] ?? $this->userService->loadUser($userId);
            }
        }, $this->repository);

        foreach ($users as $user) {
            $this->notificationService->createNotification(
                new CreateStruct([
                    'ownerId' => $user->id,
                    'type' => 'Workflow:StageChange',
                    'data' => [
                        'content_id' => $event->getWorkflowMetadata()->versionInfo->contentInfo->id,
                        'version_number' => $event->getWorkflowMetadata()->versionInfo->versionNo,
                        'language' => $event->getWorkflowMetadata()->versionInfo->initialLanguageCode,
                        'title' => $event->getWorkflowMetadata()->versionInfo->getName(),
                        'message' => $event->getTransitionMetadata()->message,
                        'sender_id' => $event->getTransitionMetadata()->userId,
                    ],
                ])
            );
        }
    }
}

class_alias(StageChangeSubscriber::class, 'EzSystems\EzPlatformWorkflow\Event\Subscriber\StageChangeSubscriber');
