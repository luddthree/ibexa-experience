<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Workflow\Event\Subscriber;

use Ibexa\AdminUi\Event\CancelEditVersionDraftEvent;
use Ibexa\ContentForms\Content\View\ContentEditView;
use Ibexa\Contracts\Core\Event\View\PostBuildViewEvent;
use Ibexa\Contracts\Core\Repository\Events\Content\BeforePublishVersionEvent;
use Ibexa\Contracts\Core\Repository\Events\Content\DeleteContentEvent;
use Ibexa\Contracts\Core\Repository\Events\Content\DeleteVersionEvent;
use Ibexa\Contracts\Core\Repository\Events\User\DeleteUserEvent;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException as APINotFoundException;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo;
use Ibexa\Contracts\Workflow\Service\WorkflowServiceInterface;
use Ibexa\Core\Base\Exceptions\NotFoundException;
use Ibexa\Scheduler\Event\ScheduledPublish;
use Ibexa\Workflow\Exception\VersionLockedException;
use Ibexa\Workflow\Registry\WorkflowRegistry;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\EnteredEvent;

final class VersionLockSubscriber implements EventSubscriberInterface
{
    /** @var \Ibexa\Contracts\Core\Repository\UserService */
    private $userService;

    /** @var \Ibexa\Contracts\Workflow\Service\WorkflowServiceInterface */
    private $workflowService;

    /** @var \Ibexa\Workflow\Registry\WorkflowRegistry */
    private $workflowRegistry;

    /** @var \Ibexa\Contracts\Core\Repository\PermissionResolver */
    private $permissionResolver;

    public function __construct(
        UserService $userService,
        WorkflowServiceInterface $workflowService,
        WorkflowRegistry $workflowRegistry,
        PermissionResolver $permissionResolver
    ) {
        $this->userService = $userService;
        $this->workflowService = $workflowService;
        $this->permissionResolver = $permissionResolver;
        $this->workflowRegistry = $workflowRegistry;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            PostBuildViewEvent::class => 'onBuildContentEditView',
            BeforePublishVersionEvent::class => 'onBeforePublishVersion',
            ScheduledPublish::class => 'onScheduledPublish',
            CancelEditVersionDraftEvent::class => 'onCancelEditVersionDraft',
            'workflow.entered' => 'onWorkflowEntered',
            DeleteUserEvent::class => 'onDeleteUser',
            DeleteContentEvent::class => 'onDeleteContent',
            DeleteVersionEvent::class => 'onDeleteVersion',
        ];
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\BadStateException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Workflow\Exception\VersionLockedException
     */
    public function onBuildContentEditView(PostBuildViewEvent $event): void
    {
        $view = $event->getView();

        if (!$view instanceof ContentEditView) {
            return;
        }

        $content = $view->getContent();
        $location = $view->getLocation();

        $workflows = $this->workflowRegistry->getSupportedWorkflows($content);

        if (!$this->hasNonInitialWorkflowPlace($workflows, $content)) {
            return;
        }

        if (!$this->permissionResolver->canUser('content', 'edit', $content, [$location])) {
            try {
                $versionLock = $this->workflowService->getVersionLock($content->versionInfo);
                $user = $this->userService->loadUser($versionLock->userId);

                throw new VersionLockedException($versionLock, $user);
            } catch (NotFoundException $exception) {
                // pass
            }
        } else {
            $this->workflowService->lockVersion($content->versionInfo);
        }
    }

    public function onCancelEditVersionDraft(CancelEditVersionDraftEvent $event): void
    {
        $this->unlockVersion($event->getContent()->versionInfo);
    }

    public function onDeleteContent(DeleteContentEvent $event)
    {
        $this->workflowService->deleteContentLocks($event->getContentInfo()->id);
    }

    public function onDeleteUser(DeleteUserEvent $event)
    {
        $this->workflowService->deleteUserLocks($event->getUser()->id);
    }

    public function onDeleteVersion(DeleteVersionEvent $event)
    {
        try {
            $this->workflowService->deleteLock(
                $this->workflowService->getVersionLock($event->getVersionInfo())
            );
        } catch (APINotFoundException $exception) {
            // pass
        }
    }

    public function onBeforePublishVersion(BeforePublishVersionEvent $event): void
    {
        $this->unlockVersion($event->getVersionInfo());
    }

    public function onScheduledPublish(ScheduledPublish $event)
    {
        $scheduledEntry = $event->getScheduledEntry();

        if (null !== $scheduledEntry->versionInfo) {
            $this->unlockVersion($scheduledEntry->versionInfo);
        }
    }

    public function onWorkflowEntered(EnteredEvent $event): void
    {
        $workflowName = $event->getWorkflowName();
        $subject = $event->getSubject();

        // guard is not needed for workflows not based on our content model
        if (!$subject instanceof Content || !$this->workflowRegistry->hasWorkflow($workflowName)) {
            return;
        }

        $this->unlockVersion($subject->versionInfo);
    }

    private function unlockVersion(VersionInfo $versionInfo): void
    {
        $isDraft = $versionInfo->isDraft();
        $canUnlock = $this->permissionResolver->canUser('content', 'unlock', $versionInfo);
        $isLocked = $this->workflowService->isVersionLocked($versionInfo);

        if ($isDraft && $isLocked && $canUnlock) {
            $this->workflowService->unlockVersion($versionInfo);
        }
    }

    private function hasNonInitialWorkflowPlace(array $workflows, Content $content): bool
    {
        foreach ($workflows as $workflow) {
            $marking = $workflow->getMarking($content);
            $places = array_keys($marking->getPlaces());
            $initialPlaces = $workflow->getDefinition()->getInitialPlaces();

            if (count(array_diff($places, $initialPlaces)) > 0) {
                return true;
            }
        }

        return false;
    }
}
