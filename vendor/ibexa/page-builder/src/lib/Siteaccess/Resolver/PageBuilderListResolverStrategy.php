<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\PageBuilder\Siteaccess\Resolver;

use Ibexa\AdminUi\Siteaccess\SiteaccessResolverInterface;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\PageBuilder\PageBuilder\ConfigurationResolverInterface;
use Ibexa\Contracts\PageBuilder\Siteaccess\Resolver\ListResolverStrategyInterface;
use Ibexa\Core\MVC\Symfony\SiteAccess;

/**
 * @internal
 */
final class PageBuilderListResolverStrategy implements ListResolverStrategyInterface
{
    private SiteaccessResolverInterface $nonAdminSiteAccessResolver;

    private ConfigurationResolverInterface $configuration;

    private SiteAccess\SiteAccessServiceInterface $siteAccessService;

    public function __construct(
        SiteaccessResolverInterface $nonAdminSiteAccessResolver,
        ConfigurationResolverInterface $configuration,
        SiteAccess\SiteAccessServiceInterface $siteAccessService
    ) {
        $this->nonAdminSiteAccessResolver = $nonAdminSiteAccessResolver;
        $this->configuration = $configuration;
        $this->siteAccessService = $siteAccessService;
    }

    /**
     * @return \Ibexa\Core\MVC\Symfony\SiteAccess[]
     */
    public function getSiteAccessListForLocation(Location $location, ?int $versionNo, ?string $languageCode): array
    {
        $pageBuilderSiteAccessList = $this->configuration->getSiteaccessList();
        $siteAccessList = $this->nonAdminSiteAccessResolver->getSiteAccessesListForLocation(
            $location,
            $versionNo,
            $languageCode
        );

        return array_values(
            array_filter(
                $siteAccessList,
                static fn (SiteAccess $siteAccess): bool => in_array($siteAccess->name, $pageBuilderSiteAccessList, true)
            )
        );
    }

    /**
     * @return \Ibexa\Core\MVC\Symfony\SiteAccess[]
     */
    public function getSiteAccessListForContent(Content $content): array
    {
        $pageBuilderSiteAccessList = $this->configuration->getSiteaccessList();

        $siteAccessList = $this->nonAdminSiteAccessResolver->getSiteAccessesListForContent($content);

        return array_values(
            array_filter(
                $siteAccessList,
                static fn (SiteAccess $siteAccess): bool => in_array($siteAccess->name, $pageBuilderSiteAccessList, true)
            )
        );
    }

    /**
     * @return \Ibexa\Core\MVC\Symfony\SiteAccess[]
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    public function getSiteAccessList(): array
    {
        return array_values(
            array_map(
                fn (string $siteAccessName): SiteAccess => $this->siteAccessService->get($siteAccessName),
                $this->configuration->getSiteaccessList(),
            )
        );
    }
}
