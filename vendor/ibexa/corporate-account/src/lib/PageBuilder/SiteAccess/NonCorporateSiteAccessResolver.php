<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\PageBuilder\SiteAccess;

use Ibexa\AdminUi\Siteaccess\SiteaccessResolverInterface;
use Ibexa\AdminUi\Specification\Location\IsRoot;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Core\MVC\Symfony\SiteAccess;
use Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessServiceInterface;
use Ibexa\CorporateAccount\Specification\IsCorporate;

final class NonCorporateSiteAccessResolver implements SiteaccessResolverInterface
{
    private SiteAccessServiceInterface $siteAccessService;

    private SiteaccessResolverInterface $inner;

    private RootLocationProvider $rootLocationProvider;

    public function __construct(
        SiteaccessResolverInterface $inner,
        SiteAccessServiceInterface $siteAccessService,
        RootLocationProvider $rootLocationProvider
    ) {
        $this->inner = $inner;
        $this->siteAccessService = $siteAccessService;
        $this->rootLocationProvider = $rootLocationProvider;
    }

    public function getSiteaccessesForLocation(
        Location $location,
        int $versionNo = null,
        string $languageCode = null
    ): array {
        return array_column($this->getSiteAccessesListForLocation($location, $versionNo, $languageCode), 'name');
    }

    public function getSiteAccessesListForLocation(
        Location $location,
        ?int $versionNo = null,
        ?string $languageCode = null
    ): array {
        if ($this->isLocationInCorporateSiteAccessRoot($location)) {
            return $this->inner->getSiteAccessesListForLocation($location, $versionNo, $languageCode);
        }

        return array_filter(
            $this->inner->getSiteAccessesListForLocation($location, $versionNo, $languageCode),
            [$this, 'isNonRootCorporateSiteAccess']
        );
    }

    public function getSiteAccessesListForContent(Content $content): array
    {
        return array_filter(
            $this->inner->getSiteAccessesListForContent($content),
            [$this, 'isNonRootCorporateSiteAccess']
        );
    }

    public function getSiteAccessesList(): array
    {
        return array_filter(
            iterator_to_array($this->siteAccessService->getAll()),
            [$this, 'isNonRootCorporateSiteAccess']
        );
    }

    private function isNonRootCorporateSiteAccess(SiteAccess $siteAccess): bool
    {
        $siteAccessRootLocation = $this->rootLocationProvider->getRootLocation($siteAccess);
        if ($siteAccessRootLocation === null) {
            return false;
        }
        $isGlobalRoot = (new IsRoot())->isSatisfiedBy($siteAccessRootLocation);
        $isCorporate = (new IsCorporate($this->siteAccessService))->isSatisfiedBy($siteAccess);

        return !$isCorporate || $isGlobalRoot;
    }

    private function isLocationInCorporateSiteAccessRoot(Location $location): bool
    {
        $corporateSiteAccessList = array_filter(
            iterator_to_array($this->siteAccessService->getAll()),
            fn (SiteAccess $siteAccess) => (new IsCorporate($this->siteAccessService))->isSatisfiedBy($siteAccess)
        );

        foreach ($corporateSiteAccessList as $siteAccess) {
            $siteAccessRootLocation = $this->rootLocationProvider->getRootLocation($siteAccess);
            if ($siteAccessRootLocation === null) {
                return false;
            }
            $isGlobalRoot = (new IsRoot())->isSatisfiedBy($siteAccessRootLocation);

            if (in_array($siteAccessRootLocation->id, $location->path) && !$isGlobalRoot) {
                return true;
            }
        }

        return false;
    }

    public function getSiteaccesses(): array
    {
        return array_column(
            $this->getSiteAccessesList(),
            'name'
        );
    }
}
