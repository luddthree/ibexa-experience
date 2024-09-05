<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Workflow\Notification;

use Ibexa\AdminUi\Strategy\NotificationTwigStrategy;
use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Core\Repository\Values\Notification\Notification;
use Ibexa\Core\Notification\Renderer\NotificationRenderer;
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
    protected $twigStrategy;

    /** @var \Ibexa\Contracts\Core\Repository\PermissionResolver */
    private $permissionResolver;

    /** @var \Ibexa\Contracts\Core\Repository\Repository */
    private $repository;

    /**
     * @param \Twig\Environment $twig
     * @param \Symfony\Component\Routing\RouterInterface $router
     * @param \Ibexa\Contracts\Core\Repository\ContentService $contentService
     * @param \Ibexa\AdminUi\Strategy\NotificationTwigStrategy $twigStrategy
     * @param \Ibexa\Contracts\Core\Repository\PermissionResolver $permissionResolver
     * @param \Ibexa\Contracts\Core\Repository\Repository $repository
     */
    public function __construct(
        Environment $twig,
        RouterInterface $router,
        ContentService $contentService,
        NotificationTwigStrategy $twigStrategy,
        PermissionResolver $permissionResolver,
        Repository $repository
    ) {
        $this->twig = $twig;
        $this->router = $router;
        $this->contentService = $contentService;
        $this->twigStrategy = $twigStrategy;
        $this->permissionResolver = $permissionResolver;
        $this->repository = $repository;
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Notification\Notification $notification
     *
     * @return string
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function render(Notification $notification): string
    {
        if (!array_key_exists('content_id', $notification->data)) {
            return '';
        }

        $this->twigStrategy->setDefault('@ibexadesign/ibexa_workflow/notification/notification.html.twig');

        $ownerContent = $this->permissionResolver->sudo(function () use ($notification) {
            return $this->contentService->loadContent($notification->data['sender_id']);
        }, $this->repository);

        $parameters = [
            'notification' => $notification,
            'title' => $notification->data['title'],
            'message' => $notification->data['message'],
            'sender_firstname' => $ownerContent->getField('first_name')->value,
            'sender_lastname' => $ownerContent->getField('last_name')->value,
        ];

        return $this->twig->render(
            $this->twigStrategy->decide($notification->data['content_id']),
            $parameters
        );
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
        if (array_key_exists('content_id', $notification->data)
            && $this->isContentAvailable($notification)
        ) {
            return $this->router->generate('ibexa.content.draft.edit', [
                'contentId' => $notification->data['content_id'],
                'versionNo' => $notification->data['version_number'],
                'language' => $notification->data['language'],
            ]);
        }

        return null;
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Notification\Notification $notification
     *
     * @return bool
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    private function isContentAvailable(Notification $notification): bool
    {
        /** @var \Ibexa\Workflow\Value\WorkflowMetadata $workflowMetadata */
        $contentInfo = $this->contentService->loadContentInfo($notification->data['content_id']);

        return !$contentInfo->isTrashed();
    }
}

class_alias(Renderer::class, 'EzSystems\EzPlatformWorkflow\Notification\Renderer');
