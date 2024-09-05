<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\SiteContext\EventSubscriber;

use Ibexa\Bundle\SiteContext\Specification\IsAdmin;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\SiteContext\SiteContextServiceInterface;
use Ibexa\Core\MVC\Symfony\Event\PreContentViewEvent;
use Ibexa\Core\MVC\Symfony\MVCEvents;
use Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessServiceInterface;
use Ibexa\Core\MVC\Symfony\View\ContentView;
use Ibexa\SiteContext\Specification\IsContextAware;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class LocationFilterSubscriber implements EventSubscriberInterface
{
    private SiteAccessServiceInterface $siteAccessService;

    private SiteContextServiceInterface $siteContextService;

    private ConfigResolverInterface $configResolver;

    public function __construct(
        SiteAccessServiceInterface $siteDefinitionAccessService,
        SiteContextServiceInterface $siteContextService,
        ConfigResolverInterface $configResolver
    ) {
        $this->siteAccessService = $siteDefinitionAccessService;
        $this->siteContextService = $siteContextService;
        $this->configResolver = $configResolver;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            MVCEvents::PRE_CONTENT_VIEW => 'onPreContentView',
        ];
    }

    public function onPreContentView(PreContentViewEvent $event): void
    {
        $view = $event->getContentView();

        if ($view instanceof ContentView && $this->isAdminSiteAccess()) {
            $location = $view->getLocation();
            if ($location instanceof Location && $this->isContextAware($location)) {
                $rootLocationId = $this->getTreeRootLocationId();
                if ($rootLocationId !== null) {
                    $this->applyContentTreeFilter($view, $rootLocationId);
                    $this->applyBreadcrumbsFilter($view, $rootLocationId);
                }
            }
        }
    }

    private function applyBreadcrumbsFilter(ContentView $view, int $rootLocationId): void
    {
        if ($view->hasParameter('path_locations')) {
            /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Location[] $segments */
            $segments = $view->getParameter('path_locations');
            foreach ($segments as $segment) {
                if ($segment->id === $rootLocationId) {
                    break;
                }

                array_shift($segments);
            }

            $view->addParameters([
                'path_locations' => $segments,
            ]);
        }
    }

    private function applyContentTreeFilter(ContentView $view, int $rootLocationId): void
    {
        $view->addParameters([
            'content_tree_module_root' => $rootLocationId,
        ]);
    }

    /**
     * Returns true if current siteaccess is administrative.
     */
    private function isAdminSiteAccess(): bool
    {
        $siteAccess = $this->siteAccessService->getCurrent();
        if ($siteAccess === null) {
            return false;
        }

        return (new IsAdmin())->isSatisfiedBy($siteAccess);
    }

    private function getTreeRootLocationId(): ?int
    {
        $siteaccess = $this->siteContextService->getCurrentContext();
        if ($siteaccess !== null) {
            return $this->configResolver->getParameter(
                'content.tree_root.location_id',
                null,
                $siteaccess->name
            );
        }

        return null;
    }

    private function isContextAware(Location $location): bool
    {
        return IsContextAware::fromConfiguration($this->configResolver)->isSatisfiedBy($location);
    }
}
