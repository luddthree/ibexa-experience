<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\PageBuilder\PageBuilder;

use Ibexa\Bundle\AdminUi\IbexaAdminUiBundle;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface as RepositoryConfigResolver;
use Ibexa\Contracts\PageBuilder\PageBuilder\ConfigurationResolverInterface;
use function in_array;

class ConfigurationResolver implements ConfigurationResolverInterface
{
    /** @var \Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface */
    private $repositoryConfigResolver;

    /** @var array */
    private $siteaccessGroups;

    /** @var string[] */
    private $siteAccesses;

    /**
     * @param \Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface $repositoryConfigResolver
     * @param array $siteaccessGroups
     * @param string[] $siteAccesses
     */
    public function __construct(
        RepositoryConfigResolver $repositoryConfigResolver,
        array $siteaccessGroups,
        array $siteAccesses
    ) {
        $this->repositoryConfigResolver = $repositoryConfigResolver;
        $this->siteaccessGroups = $siteaccessGroups;
        $this->siteAccesses = $siteAccesses;
    }

    /**
     * @param string|null $siteaccess
     *
     * @return array
     */
    public function getSiteaccessList(string $siteaccess = null): array
    {
        $pageBuilderSiteaccesses = $this->repositoryConfigResolver->getParameter(
            'page_builder.siteaccess_list',
            null,
            $siteaccess
        );

        return array_intersect($this->siteAccesses, $pageBuilderSiteaccesses);
    }

    public function getSiteaccessHosts(string $siteaccessName): array
    {
        $compatibleAdminSiteaccesses = $this->reverseAdminSiteaccessMatch(
            $siteaccessName
        );

        $siteAccessHosts = [];
        foreach ($compatibleAdminSiteaccesses as $adminSiteaccess) {
            $siteAccessHosts = array_merge(
                $siteAccessHosts,
                $this->get('siteaccess_hosts', $adminSiteaccess)
            );
        }

        return array_values(array_unique($siteAccessHosts));
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
        $parameter = sprintf('page_builder.%s', $name);

        if (!$this->repositoryConfigResolver->hasParameter($parameter)) {
            return $fallbackValue;
        }

        return $this->repositoryConfigResolver->getParameter($parameter, null, $siteaccess);
    }

    /**
     * {@inheritdoc}
     */
    public function reverseAdminSiteaccessMatch(string $siteaccessName): array
    {
        $adminGroupSiteaccesses = $this->siteaccessGroups[IbexaAdminUiBundle::ADMIN_GROUP_NAME];

        return array_filter(
            $adminGroupSiteaccesses,
            function (string $adminSiteaccessName) use ($siteaccessName) {
                return in_array(
                    $siteaccessName,
                    $this->getSiteaccessList($adminSiteaccessName),
                    true
                );
            }
        );
    }
}

class_alias(ConfigurationResolver::class, 'EzSystems\EzPlatformPageBuilder\PageBuilder\ConfigurationResolver');
