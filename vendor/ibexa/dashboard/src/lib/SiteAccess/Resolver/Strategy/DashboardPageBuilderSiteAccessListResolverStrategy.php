<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Dashboard\SiteAccess\Resolver\Strategy;

use Ibexa\AdminUi\Specification\SiteAccess\IsAdmin;
use Ibexa\Contracts\Core\Persistence\Content\Location\Handler;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\PageBuilder\Siteaccess\Resolver\ListResolverStrategyInterface;
use Ibexa\Core\MVC\Symfony\SiteAccess;
use Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessServiceInterface;
use Ibexa\Dashboard\Specification\IsDashboardContentType;
use Ibexa\Dashboard\Specification\IsInDashboardTreeRoot;

/**
 * @internal
 */
final class DashboardPageBuilderSiteAccessListResolverStrategy implements ListResolverStrategyInterface
{
    private SiteAccessServiceInterface $siteAccessService;

    /** @var array<string, string[]> */
    private array $siteAccessGroups;

    private ConfigResolverInterface $configResolver;

    private Handler $locationHandler;

    /**
     * @param array<string, string[]> $siteAccessGroups
     */
    public function __construct(
        SiteAccessServiceInterface $siteAccessService,
        ConfigResolverInterface $configResolver,
        Handler $locationHandler,
        array $siteAccessGroups
    ) {
        $this->siteAccessService = $siteAccessService;
        $this->siteAccessGroups = $siteAccessGroups;
        $this->configResolver = $configResolver;
        $this->locationHandler = $locationHandler;
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function getSiteAccessListForLocation(Location $location, ?int $versionNo, ?string $languageCode): ?array
    {
        $current = $this->siteAccessService->getCurrent();

        if (
            $current !== null
            && $this->isCurrentSiteAccessAdmin($current)
            && $this->isInDashboardTreeRoot($location)
        ) {
            return [$current];
        }

        return null;
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function getSiteAccessListForContent(Content $content): ?array
    {
        $current = $this->siteAccessService->getCurrent();
        if (
            $current !== null
            && $this->isCurrentSiteAccessAdmin($current)
            && $this->isDashboardContent($content)
        ) {
            return [$current];
        }

        return null;
    }

    public function getSiteAccessList(): ?array
    {
        // unable to determine the list without context, another strategy should be used
        return null;
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    private function isCurrentSiteAccessAdmin(SiteAccess $siteAccess): bool
    {
        return (new IsAdmin($this->siteAccessGroups))->isSatisfiedBy($siteAccess);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    private function isDashboardContent(Content $content): bool
    {
        return (new IsDashboardContentType($this->configResolver))->isSatisfiedBy($content->getContentType());
    }

    private function isInDashboardTreeRoot(Location $location): bool
    {
        return (new IsInDashboardTreeRoot($this->configResolver, $this->locationHandler))->isSatisfiedBy($location);
    }
}
