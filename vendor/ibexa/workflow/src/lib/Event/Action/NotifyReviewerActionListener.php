<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Workflow\Event\Action;

use Ibexa\Contracts\Core\Repository\NotificationService;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\Core\Repository\Values\Notification\CreateStruct;
use Ibexa\Contracts\Workflow\Event\Action\AbstractStageWorkflowActionListener;
use Symfony\Component\Workflow\Event\EnteredEvent;

class NotifyReviewerActionListener extends AbstractStageWorkflowActionListener
{
    /** @var \Ibexa\Contracts\Core\Repository\UserService */
    private $userService;

    /** @var \Ibexa\Contracts\Core\Repository\NotificationService */
    private $notificationService;

    /** @var \Ibexa\Core\Repository\Permission\PermissionResolver PermissionResolver */
    private $permissionResolver;

    public function __construct(
        UserService $userService,
        NotificationService $notificationService,
        PermissionResolver $permissionResolver
    ) {
        $this->userService = $userService;
        $this->notificationService = $notificationService;
        $this->permissionResolver = $permissionResolver;
    }

    public function getIdentifier(): string
    {
        return 'notify_reviewer';
    }

    public function onWorkflowEvent(EnteredEvent $event): void
    {
        $marking = $event->getWorkflow()->getMarkingStore()->getMarking($event->getSubject());

        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Content $content */
        $content = $event->getSubject();
        $versionInfo = $content->getVersionInfo();
        $context = $marking->getContext() ?? [];

        if (empty($context['reviewerId'])) {
            return;
        }

        $sender = $this->userService->loadUser(
            $this->permissionResolver->getCurrentUserReference()->getUserId()
        );

        $notification = new CreateStruct();
        $notification->ownerId = $context['reviewerId'];
        $notification->type = 'Workflow:NotifyReviewer';
        $notification->data = [
            'content_id' => $content->id,
            'content_name' => $content->getName(),
            'version_number' => $versionInfo->versionNo,
            'language_code' => $versionInfo->initialLanguageCode,
            'sender_id' => $sender->id,
            'sender_name' => $sender->getName(),
            'message' => $context['message'] ?? '',
        ];

        $this->notificationService->createNotification($notification);
    }
}

class_alias(NotifyReviewerActionListener::class, 'EzSystems\EzPlatformWorkflow\Event\Action\NotifyReviewerActionListener');
