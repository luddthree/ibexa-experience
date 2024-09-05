<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Config\Host;

use Ibexa\AdminUi\Siteaccess\SiteaccessResolverInterface;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Personalization\Config\ItemType\IncludedItemTypeResolverInterface;
use Ibexa\Personalization\SiteAccess\ScopeParameterResolver;

/**
 * @internal
 */
final class HostResolver implements HostResolverInterface
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

    public function resolveUrl(Content $content, string $languageCode): ?string
    {
        $hostUrls = [];
        foreach ($this->siteaccessResolver->getSiteAccessesListForContent($content) as $siteAccess) {
            $siteAccessName = $siteAccess->name;
            if (
                !$this->includedItemTypeResolver->isContentTypeIncludedInSiteAccess(
                    $content->getContentType(),
                    $siteAccessName
                )
            ) {
                continue;
            }

            $configuredLanguages = $this->getConfiguredLanguages($siteAccessName);
            foreach ($content->getVersionInfo()->getLanguages() as $language) {
                $code = $language->getLanguageCode();
                if (!in_array($code, $configuredLanguages, true)) {
                    continue;
                }

                $hostUrls[$code] = $this->scopeParameterResolver->getHostUrlForScope($siteAccess);
            }
        }

        return $hostUrls[$languageCode] ?? null;
    }

    /**
     * @return array<string>
     */
    private function getConfiguredLanguages(string $siteAccessName): array
    {
        return $this->configResolver->getParameter(
            'languages',
            null,
            $siteAccessName
        );
    }
}
