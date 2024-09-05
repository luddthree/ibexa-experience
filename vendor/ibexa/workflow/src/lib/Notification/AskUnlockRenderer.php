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
use Ibexa\Contracts\Core\Repository\Values\Notification\Notification;
use Ibexa\Contracts\Workflow\Service\WorkflowServiceInterface;
use Ibexa\Core\Notification\Renderer\NotificationRenderer;
use Twig\Environment;

final class AskUnlockRenderer implements NotificationRenderer
{
    /** @var \Twig\Environment */
    private $twig;

    /** @var \Ibexa\AdminUi\Strategy\NotificationTwigStrategy */
    private $twigStrategy;

    /** @var \Ibexa\Contracts\Core\Repository\ContentService */
    private $contentService;

    /** @var \Ibexa\Contracts\Workflow\Service\WorkflowServiceInterface */
    private $workflowService;

    public function __construct(
        Environment $twig,
        NotificationTwigStrategy $twigStrategy,
        ContentService $contentService,
        WorkflowServiceInterface $workflowService
    ) {
        $this->twig = $twig;
        $this->twigStrategy = $twigStrategy;
        $this->contentService = $contentService;
        $this->workflowService = $workflowService;
    }

    public function render(Notification $notification): string
    {
        $data = $notification->data;
        $contentId = $data['contentId'];
        $version = $data['versionNumber'];
        $sender = $data['sender'];

        $this->twigStrategy->setDefault('@ibexadesign/ibexa_workflow/notification/ask_unlock_notification.html.twig');

        try {
            $content = $this->contentService->loadContent($contentId, null, $version);
        } catch (NotFoundException $exception) {
            return '';
        }

        $isLocked = $this->workflowService->isVersionLocked($content->versionInfo, $notification->ownerId);

        return $this->twig->render(
            $this->twigStrategy->decide($contentId),
            [
                'notification' => $notification,
                'version_lock' => $isLocked ? $this->workflowService->getVersionLock($content->versionInfo) : null,
                'title' => $content->getName(),
                'sender' => $sender,
            ]
        );
    }

    public function generateUrl(Notification $notification): ?string
    {
        return null;
    }
}
