<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Config\Authentication;

use Ibexa\AdminUi\Siteaccess\SiteaccessResolverInterface;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Core\MVC\Symfony\SiteAccess;
use Ibexa\Personalization\Config\ItemType\IncludedItemTypeResolverInterface;
use Ibexa\Personalization\SiteAccess\ScopeParameterResolver;
use Ibexa\Personalization\Value\Authentication\Parameters;

/**
 * @internal
 */
final class ParametersResolver implements ParametersResolverInterface
{
    private ConfigResolverInterface $configResolver;

    private IncludedItemTypeResolverInterface $includedItemTypeResolver;

    private ScopeParameterResolver $scopeParameterResolver;

    private SiteaccessResolverInterface $siteaccessResolver;

    public function __construct(
        ConfigResolverInterface $configResolver,
        IncludedItemTypeResolverInterface $includedItemTypeResolver,
        ScopeParameterResolver $scopeParameterResolver,
        SiteaccessResolverInterface $siteaccessResolver
    ) {
        $this->configResolver = $configResolver;
        $this->includedItemTypeResolver = $includedItemTypeResolver;
        $this->scopeParameterResolver = $scopeParameterResolver;
        $this->siteaccessResolver = $siteaccessResolver;
    }

    public function resolveForContent(Content $content): ?Parameters
    {
        $siteAccessList = $this->siteaccessResolver->getSiteAccessesListForContent($content);
        foreach ($siteAccessList as $siteAccess) {
            if (
                !$this->includedItemTypeResolver->isContentTypeIncludedInSiteAccess(
                    $content->getContentType(),
                    $siteAccess->name
                )
            ) {
                continue;
            }

            return $this->getParametersForScope($siteAccess);
        }

        return null;
    }

    /**
     * @return array<\Ibexa\Personalization\Value\Authentication\Parameters>
     */
    public function resolveAllForContent(Content $content): array
    {
        $siteAccessList = $this->siteaccessResolver->getSiteAccessesListForContent($content);
        $authenticationParameters = [];
        foreach ($siteAccessList as $siteAccess) {
            $siteAccessName = $siteAccess->name;
            if (
                !$this->includedItemTypeResolver->isContentTypeIncludedInSiteAccess(
                    $content->getContentType(),
                    $siteAccessName
                )
            ) {
                continue;
            }

            $parameters = $this->getParametersForScope($siteAccess);
            if (null === $parameters) {
                continue;
            }

            $languages = $this->configResolver->getParameter('languages', null, $siteAccessName);
            foreach ($content->getVersionInfo()->getLanguages() as $language) {
                $languageCode = $language->getLanguageCode();
                if (!in_array($languageCode, $languages, true)) {
                    continue;
                }

                $authenticationParameters[$languageCode] = $parameters;
            }
        }

        return $authenticationParameters;
    }

    private function getParametersForScope(SiteAccess $siteAccess): ?Parameters
    {
        $customerId = $this->scopeParameterResolver->getCustomerIdForScope($siteAccess);
        $licenseKey = $this->scopeParameterResolver->getLicenseKeyForScope($siteAccess);

        if (null === $customerId || null === $licenseKey) {
            return null;
        }

        return new Parameters($customerId, $licenseKey);
    }
}
