<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Workflow\Notification;

use Ibexa\AdminUi\Strategy\NotificationTwigStrategy;
use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException;
use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\Core\Repository\Values\Notification\Notification;
use Ibexa\Core\Notification\Renderer\NotificationRenderer;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

class NotifyReviewerActionRenderer implements NotificationRenderer
{
    /** @var \Twig\Environment */
    protected $twig;

    /** @var \Symfony\Component\Routing\RouterInterface */
    protected $router;

    /** @var \Ibexa\Contracts\Core\Repository\ContentService */
    protected $contentService;

    /** @var \Ibexa\Contracts\Core\Repository\UserService */
    protected $userService;

    /** @var \Ibexa\AdminUi\Strategy\NotificationTwigStrategy */
    protected $twigStrategy;

    public function __construct(
        Environment $twig,
        RouterInterface $router,
        ContentService $contentService,
        UserService $userService,
        NotificationTwigStrategy $twigStrategy
    ) {
        $this->twig = $twig;
        $this->router = $router;
        $this->contentService = $contentService;
        $this->userService = $userService;
        $this->twigStrategy = $twigStrategy;
    }

    public function render(Notification $notification): string
    {
        $data = $notification->data;
        $contentId = $data['content_id'];
        $title = $data['content_name'];
        $sender = $data['sender_name'];
        $message = $data['message'];

        $this->twigStrategy->setDefault('@ibexadesign/ibexa_workflow/notification/notify_reviewer_notification.html.twig');

        return $this->twig->render(
            $this->twigStrategy->decide($contentId),
            [
                'notification' => $notification,
                'message' => $message,
                'sender' => $sender,
                'title' => $title,
            ]
        );
    }

    public function generateUrl(Notification $notification): ?string
    {
        $contentId = $notification->data['content_id'];
        $languageCode = $notification->data['language_code'];
        $versionNo = $notification->data['version_number'];

        if ($this->isContentAvailable($contentId, $versionNo, $languageCode)) {
            return $this->router->generate('ibexa.content.draft.edit', [
                'contentId' => $contentId,
                'versionNo' => $versionNo,
                'language' => $languageCode,
            ]);
        }

        return null;
    }

    private function isContentAvailable(int $contentId, int $versionNo, string $languageCode): bool
    {
        try {
            $content = $this->contentService->loadContent($contentId, [$languageCode], $versionNo);

            return $content->versionInfo->isDraft();
        } catch (NotFoundException | UnauthorizedException $e) {
            return false;
        }
    }
}

class_alias(NotifyReviewerActionRenderer::class, 'EzSystems\EzPlatformWorkflow\Notification\NotifyReviewerActionRenderer');
