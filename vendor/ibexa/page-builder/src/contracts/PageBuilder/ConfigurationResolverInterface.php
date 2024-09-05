<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\PageBuilder\PageBuilder;

interface ConfigurationResolverInterface
{
    /**
     * Returns admin siteaccesses configured to handle $siteaccessName.
     *
     * @param string $siteaccessName
     *
     * @return string[]
     */
    public function reverseAdminSiteaccessMatch(string $siteaccessName): array;

    /**
     * @param string|null $siteaccess
     *
     * @return string[]
     */
    public function getSiteaccessList(string $siteaccess = null): array;

    /**
     * @param string $siteaccessName
     *
     * @return string[]
     */
    public function getSiteaccessHosts(string $siteaccessName): array;

    /**
     * @param string $name
     * @param string|null $siteaccess
     * @param mixed $fallbackValue
     *
     * @return mixed
     */
    public function get(string $name, string $siteaccess = null, $fallbackValue = null);
}

class_alias(ConfigurationResolverInterface::class, 'EzSystems\EzPlatformPageBuilder\PageBuilder\ConfigurationResolverInterface');
