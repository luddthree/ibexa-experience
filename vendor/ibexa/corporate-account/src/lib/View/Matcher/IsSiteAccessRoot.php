<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\View\Matcher;

use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Core\MVC\Symfony\Matcher\ContentBased\MultipleValued;
use Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessServiceInterface;
use Ibexa\Core\MVC\Symfony\View\ContentValueView;
use Ibexa\Core\MVC\Symfony\View\LocationValueView;
use Ibexa\Core\MVC\Symfony\View\View;
use Ibexa\CorporateAccount\PageBuilder\SiteAccess\RootLocationProvider;

final class IsSiteAccessRoot extends MultipleValued
{
    private SiteAccessServiceInterface $siteAccessService;

    private RootLocationProvider $rootLocationProvider;

    public function __construct(
        SiteAccessServiceInterface $siteAccessService,
        RootLocationProvider $rootLocationProvider
    ) {
        $this->siteAccessService = $siteAccessService;
        $this->rootLocationProvider = $rootLocationProvider;
    }

    public function matchLocation(Location $location): bool
    {
        $rootLocation = $this->getRootLocation();

        return $rootLocation !== null && $location->id === $rootLocation->id;
    }

    public function matchContentInfo(ContentInfo $contentInfo): bool
    {
        $rootLocation = $this->getRootLocation();
        $contentMainLocation = $contentInfo->getMainLocation();

        return ($contentMainLocation !== null) && ($rootLocation !== null) && ($contentMainLocation->id === $rootLocation->id);
    }

    public function match(View $view): ?bool
    {
        if ($view instanceof LocationValueView) {
            return $this->matchLocation($view->getLocation());
        }

        if ($view instanceof ContentValueView) {
            return $this->matchContentInfo($view->getContent()->contentInfo);
        }

        return false;
    }

    private function getRootLocation(): ?Location
    {
        $siteAccess = $this->siteAccessService->getCurrent();
        if ($siteAccess === null) {
            return null;
        }

        return $this->rootLocationProvider->getRootLocation($siteAccess);
    }
}
