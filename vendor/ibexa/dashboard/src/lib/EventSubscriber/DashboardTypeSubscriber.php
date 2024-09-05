<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Dashboard\EventSubscriber;

use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Dashboard\Specification\IsDashboardContentType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RouterInterface;

final class DashboardTypeSubscriber implements EventSubscriberInterface
{
    private ContentTypeService $contentTypeService;

    private ConfigResolverInterface $configResolver;

    private RouterInterface $router;

    public function __construct(
        ContentTypeService $contentTypeService,
        ConfigResolverInterface $configResolver,
        RouterInterface $router
    ) {
        $this->contentTypeService = $contentTypeService;
        $this->configResolver = $configResolver;
        $this->router = $router;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelController'],
        ];
    }

    public function onKernelController(RequestEvent $event): void
    {
        if (!$this->isContentTypeRedirect($event)) {
            return;
        }

        $dashboardTypeIdentifier = $this->configResolver->getParameter(
            IsDashboardContentType::CONTENT_TYPE_IDENTIFIER_PARAM_NAME
        );
        $dashboardType = $this->contentTypeService->loadContentTypeByIdentifier($dashboardTypeIdentifier);
        $dashboardTypeGroups = $dashboardType->getContentTypeGroups();
        $dashboardTypeGroup = reset($dashboardTypeGroups);
        $contentTypeGroupId = (int)$event->getRequest()->get('contentTypeGroupId');

        if ($dashboardTypeGroup === false || $dashboardTypeGroup->id !== $contentTypeGroupId) {
            return;
        }

        $redirectUrl = $this->router->generate('ibexa.dashboard.content_type');
        $event->setResponse(new RedirectResponse($redirectUrl));
    }

    private function isContentTypeRedirect(RequestEvent $event): bool
    {
        return $event->getRequest()->get('_route') === 'ibexa.content_type.view'
            || $event->getRequest()->get('_route') === 'ibexa.content_type_group.view';
    }
}
