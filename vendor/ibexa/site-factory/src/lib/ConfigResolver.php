<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\SiteFactory;

use Ibexa\Bundle\Core\DependencyInjection\Configuration\ConfigResolver\SiteAccessConfigResolver;
use Ibexa\Contracts\SiteFactory\Values\Site\PublicAccess;
use Ibexa\Core\MVC\Exception\ParameterNotFoundException;
use Ibexa\Core\MVC\Symfony\SiteAccess;
use Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessProviderInterface;
use Ibexa\SiteFactory\Service\PublicAccessService;

class ConfigResolver extends SiteAccessConfigResolver
{
    /** @var \Ibexa\SiteFactory\Service\PublicAccessService */
    protected $publicAccessService;

    public function __construct(
        PublicAccessService $publicAccessService,
        SiteAccessProviderInterface $siteAccessProvider,
        string $defaultNamespace
    ) {
        parent::__construct($siteAccessProvider, $defaultNamespace);
        $this->publicAccessService = $publicAccessService;
    }

    protected function resolverHasParameter(SiteAccess $siteAccess, string $paramName, string $namespace): bool
    {
        return $this->getPublicAccess($siteAccess)->hasConfigParameter($namespace, $paramName);
    }

    protected function getParameterFromResolver(SiteAccess $siteAccess, string $paramName, string $namespace)
    {
        $site = $this->getPublicAccess($siteAccess);
        if ($site->hasConfigParameter($namespace, $paramName)) {
            return $site->getConfigParameter($namespace, $paramName);
        }

        throw new ParameterNotFoundException($paramName, $namespace, [$siteAccess->name]);
    }

    protected function isSiteAccessSupported(SiteAccess $siteAccess): bool
    {
        return SiteAccessProvider::class === $siteAccess->provider;
    }

    private function getPublicAccess(SiteAccess $siteAccess): PublicAccess
    {
        return $this->publicAccessService->loadPublicAccess($siteAccess->name);
    }
}

class_alias(ConfigResolver::class, 'EzSystems\EzPlatformSiteFactory\ConfigResolver');
