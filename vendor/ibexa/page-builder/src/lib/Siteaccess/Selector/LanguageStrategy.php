<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\PageBuilder\Siteaccess\Selector;

use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\PageBuilder\Siteaccess\Selector\Context;
use Ibexa\Contracts\PageBuilder\Siteaccess\Selector\SelectorStrategy;

final class LanguageStrategy implements SelectorStrategy
{
    private ConfigResolverInterface $configResolver;

    public function __construct(ConfigResolverInterface $configResolver)
    {
        $this->configResolver = $configResolver;
    }

    public function selectSiteAccess(Context $context, array $siteAccessNameList): ?string
    {
        $language = $context->language;

        // try different site accesses to match Content's language
        foreach ($siteAccessNameList as $siteAccess) {
            $languages = $this->getLanguages($siteAccess);
            if (isset($languages[0]) && $language->languageCode === $languages[0]) {
                return $siteAccess;
            }
        }

        foreach ($siteAccessNameList as $siteAccess) {
            $languages = $this->getLanguages($siteAccess);
            if (in_array($language->languageCode, $languages, true)) {
                return $siteAccess;
            }
        }

        return null;
    }

    private function getLanguages(string $siteAccessName): array
    {
        return $this->configResolver->getParameter(
            'languages',
            null,
            $siteAccessName
        );
    }
}
