<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Scheduler\Notification;

use Ibexa\AdminUi\Strategy\NotificationTwigStrategy;
use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Values\Notification\Notification;
use Ibexa\Core\Notification\Renderer\NotificationRenderer;
use Ibexa\Scheduler\ValueObject\NotificationFactory;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

class Renderer implements NotificationRenderer
{
    /** @var \Twig\Environment */
    protected $twig;

    /** @var \Symfony\Component\Routing\RouterInterface */
    protected $router;

    /** @var \Ibexa\Contracts\Core\Repository\ContentService */
    protected $contentService;

    /** @var \Ibexa\AdminUi\Strategy\NotificationTwigStrategy */
    private $twigStrategy;

    /**
     * @param \Twig\Environment $twig
     * @param \Symfony\Component\Routing\RouterInterface $router
     * @param \Ibexa\Contracts\Core\Repository\ContentService $contentService
     * @param \Ibexa\AdminUi\Strategy\NotificationTwigStrategy $twigStrategy
     */
    public function __construct(
        Environment $twig,
        RouterInterface $router,
        ContentService $contentService,
        NotificationTwigStrategy $twigStrategy
    ) {
        $this->twig = $twig;
        $this->router = $router;
        $this->contentService = $contentService;
        $this->twigStrategy = $twigStrategy;
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Notification\Notification $notification
     *
     * @return string
     *
     * @throws \Ibexa\AdminUi\Exception\NoValidResultException
     */
    public function render(Notification $notification): string
    {
        list(
            $category,
            $type
        ) = explode(':', $notification->type);

        $this->twigStrategy->setDefault('@IbexaScheduler/notification.html.twig');

        if (!\array_key_exists('contentId', $notification->data)) {
            return '';
        }

        return $this->twig->render($this->twigStrategy->decide($notification->data['contentId']), [
            'notification' => $notification,
            'is_published' => ($type === NotificationFactory::TYPE_PUBLISHED),
            'is_canceled' => ($type === NotificationFactory::TYPE_UNSCHEDULED),
            'is_hidden' => ($type === NotificationFactory::TYPE_HIDDEN),
            'title' => $notification->data['contentName'],
            'message' => $notification->data['message'],
        ]);
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Notification\Notification $notification
     *
     * @return string|null
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function generateUrl(Notification $notification): ?string
    {
        if (\array_key_exists('isAvailable', $notification->data) &&
            $notification->data['isAvailable'] &&
            $this->isContentAvailable($notification)
        ) {
            $contentInfo = $this->contentService->loadContentInfo($notification->data['contentId']);
            $hasLocation = (bool)$contentInfo->mainLocationId;

            if ($hasLocation) {
                return $this->router->generate('ibexa.content.view', [
                    'contentId' => $contentInfo->id,
                ]);
            } else {
                return $this->router->generate('ibexa.content.draft.edit', [
                    'contentId' => $contentInfo->id,
                    'versionNo' => $contentInfo->currentVersionNo,
                    'language' => $contentInfo->mainLanguageCode,
                ]);
            }
        }

        return null;
    }

    private function isContentAvailable(Notification $notification): bool
    {
        try {
            $contentInfo = $this->contentService->loadContentInfo($notification->data['contentId']);

            return !$contentInfo->isTrashed();
        } catch (NotFoundException $exception) {
            return false;
        }
    }
}

class_alias(Renderer::class, 'EzSystems\DateBasedPublisher\Core\Notification\Renderer');
