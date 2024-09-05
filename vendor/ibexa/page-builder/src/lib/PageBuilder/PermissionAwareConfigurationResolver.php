<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\PageBuilder\PageBuilder;

use Ibexa\Contracts\PageBuilder\PageBuilder\ConfigurationResolverInterface;
use Ibexa\Contracts\PageBuilder\Siteaccess\SiteaccessServiceInterface;

class PermissionAwareConfigurationResolver implements ConfigurationResolverInterface
{
    /**
     * @var \Ibexa\Contracts\PageBuilder\PageBuilder\ConfigurationResolverInterface
     */
    private $configurationResolver;

    /**
     * @var \Ibexa\Contracts\PageBuilder\Siteaccess\SiteaccessServiceInterface
     */
    private $siteaccessService;

    /**
     * @param \Ibexa\Contracts\PageBuilder\PageBuilder\ConfigurationResolverInterface $configurationResolver
     * @param \Ibexa\Contracts\PageBuilder\Siteaccess\SiteaccessServiceInterface $siteaccessService
     */
    public function __construct(
        ConfigurationResolverInterface $configurationResolver,
        SiteaccessServiceInterface $siteaccessService
    ) {
        $this->configurationResolver = $configurationResolver;
        $this->siteaccessService = $siteaccessService;
    }

    /**
     * @param string|null $siteaccess
     *
     * @return array
     */
    public function getSiteaccessList(string $siteaccess = null): array
    {
        $siteaccesses = $this->configurationResolver->getSiteaccessList($siteaccess);

        return $this->siteaccessService->filterAvailableSiteaccesses($siteaccesses);
    }

    public function getSiteaccessHosts(string $siteaccessName): array
    {
        return $this->configurationResolver->getSiteaccessHosts($siteaccessName);
    }

    /**
     * @param string $name
     * @param string|null $siteaccess
     * @param mixed $fallbackValue
     *
     * @return mixed
     */
    public function get(string $name, string $siteaccess = null, $fallbackValue = null)
    {
        return $this->configurationResolver->get($name, $siteaccess, $fallbackValue);
    }

    /**
     * {@inheritdoc}
     */
    public function reverseAdminSiteaccessMatch(string $siteaccessName): array
    {
        return $this->configurationResolver->reverseAdminSiteaccessMatch($siteaccessName);
    }
}

class_alias(PermissionAwareConfigurationResolver::class, 'EzSystems\EzPlatformPageBuilder\PageBuilder\PermissionAwareConfigurationResolver');
