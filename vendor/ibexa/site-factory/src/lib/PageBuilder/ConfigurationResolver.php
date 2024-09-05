<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\SiteFactory\PageBuilder;

use Ibexa\Contracts\PageBuilder\PageBuilder\ConfigurationResolverInterface;
use Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessServiceInterface;
use Ibexa\SiteFactory\SiteAccessProvider;

class ConfigurationResolver implements ConfigurationResolverInterface
{
    /** @var \Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessServiceInterface */
    private $siteAccessService;

    /** @var \Ibexa\Contracts\PageBuilder\PageBuilder\ConfigurationResolverInterface */
    private $inner;

    /**
     * @param \Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface $inner
     * @param \Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessServiceInterface $siteAccessService
     */
    public function __construct(
        ConfigurationResolverInterface $inner,
        SiteAccessServiceInterface $siteAccessService
    ) {
        $this->siteAccessService = $siteAccessService;
        $this->inner = $inner;
    }

    /**
     * @param string|null $siteaccess
     *
     * @return array
     */
    public function getSiteaccessList(string $siteAccess = null): array
    {
        $pageBuilderSiteAccesses = $this->inner->getSiteaccessList($siteAccess);

        $siteFactorySiteAccesses = [];
        foreach ($this->siteAccessService->getAll() as $siteAccess) {
            if ($siteAccess->provider === SiteAccessProvider::class) {
                $siteFactorySiteAccesses[] = $siteAccess->name;
            }
        }

        return array_merge($pageBuilderSiteAccesses, $siteFactorySiteAccesses);
    }

    /**
     * @return array
     */
    public function getSiteaccessHosts(string $siteaccessName): array
    {
        return $this->inner->getSiteaccessHosts($siteaccessName);
    }

    /**
     * {@inheritdoc}
     */
    public function reverseAdminSiteaccessMatch(string $siteaccessName): array
    {
        return $this->inner->reverseAdminSiteaccessMatch($siteaccessName);
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $name, string $siteaccess = null, $fallbackValue = null)
    {
        return $this->inner->get($name, $siteaccess, $fallbackValue);
    }
}

class_alias(ConfigurationResolver::class, 'EzSystems\EzPlatformSiteFactory\PageBuilder\ConfigurationResolver');
