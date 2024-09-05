<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\SiteAccess;

use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Core\MVC\Symfony\SiteAccess;
use Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessServiceInterface;

/**
 * @internal
 */
final class ScopeParameterNativeResolver implements ScopeParameterResolver
{
    private const AUTHENTICATION_CUSTOMER_ID = 'personalization.authentication.customer_id';
    private const AUTHENTICATION_LICENSE_KEY = 'personalization.authentication.license_key';
    private const HOST_URI = 'personalization.host_uri';

    private const SITE_NAME = 'site_name';

    public ConfigResolverInterface $configResolver;

    public SiteAccessServiceInterface $siteAccessService;

    public function __construct(
        ConfigResolverInterface $configResolver,
        SiteAccessServiceInterface $siteAccessService
    ) {
        $this->configResolver = $configResolver;
        $this->siteAccessService = $siteAccessService;
    }

    public function getCustomerIdForCurrentScope(): ?int
    {
        $currentSiteAccess = $this->siteAccessService->getCurrent();
        if (null === $currentSiteAccess) {
            return null;
        }

        return $this->getCustomerIdForScope($currentSiteAccess);
    }

    public function getCustomerIdForScope(SiteAccess $siteAccess): ?int
    {
        $customerId = (int)$this->getParameterForScope(
            $siteAccess,
            self::AUTHENTICATION_CUSTOMER_ID
        );

        return $customerId > 0 ? $customerId : null;
    }

    public function getLicenseKeyForScope(SiteAccess $siteAccess): ?string
    {
        $licenseKey = $this->getParameterForScope(
            $siteAccess,
            self::AUTHENTICATION_LICENSE_KEY
        );

        return !empty($licenseKey) ? $licenseKey : null;
    }

    public function getHostUrlForScope(SiteAccess $siteAccess): ?string
    {
        $hostUrl = $this->getParameterForScope(
            $siteAccess,
            self::HOST_URI
        );

        return !empty($hostUrl) ? $hostUrl : null;
    }

    public function getSiteNameForScope(SiteAccess $siteAccess): ?string
    {
        return $this->getParameterForScope(
            $siteAccess,
            self::SITE_NAME,
        );
    }

    private function getParameterForScope(SiteAccess $siteAccess, string $paramName): ?string
    {
        if (!$this->configResolver->hasParameter(
            $paramName,
            null,
            $siteAccess->name
        )) {
            return null;
        }

        return (string)$this->configResolver->getParameter(
            $paramName,
            null,
            $siteAccess->name
        );
    }
}

class_alias(ScopeParameterNativeResolver::class, 'Ibexa\Platform\Personalization\SiteAccess\ScopeParameterNativeResolver');
