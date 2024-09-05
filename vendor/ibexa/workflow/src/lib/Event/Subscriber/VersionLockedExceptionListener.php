<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Workflow\Event\Subscriber;

use Ibexa\Contracts\AdminUi\Notification\TranslatableNotificationHandlerInterface;
use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Workflow\Exception\VersionLockedException;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class VersionLockedExceptionListener
{
    /** @var \Symfony\Component\Routing\Generator\UrlGeneratorInterface */
    private $urlGenerator;

    /** @var \Ibexa\Contracts\Core\Repository\ContentService */
    private $contentService;

    /** @var \Ibexa\Contracts\AdminUi\Notification\TranslatableNotificationHandlerInterface */
    private $notificationHandler;

    public function __construct(
        UrlGeneratorInterface $urlGenerator,
        ContentService $contentService,
        TranslatableNotificationHandlerInterface $notificationHandler
    ) {
        $this->urlGenerator = $urlGenerator;
        $this->contentService = $contentService;
        $this->notificationHandler = $notificationHandler;
    }

    public function onVersionLocked(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if ($exception instanceof VersionLockedException) {
            $user = $exception->getUser();
            $versionLock = $exception->getVersionLock();

            $this->notificationHandler->warning(
                /** @Desc("Version is assigned to another user (%name%).") */
                'draft.edit.locked.notification',
                ['%name%' => $user->getName()],
                'ibexa_workflow'
            );

            $content = $this->contentService->loadContent($versionLock->contentId);

            $route = $content->contentInfo->isDraft()
                ? $this->urlGenerator->generate('ibexa.dashboard')
                : $this->urlGenerator->generate('ibexa.content.view', [
                    'contentId' => $versionLock->contentId,
                ]);

            $event->setResponse(new RedirectResponse($route));
        }
    }
}
