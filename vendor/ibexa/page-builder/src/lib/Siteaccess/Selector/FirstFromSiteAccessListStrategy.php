<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\PageBuilder\Siteaccess\Selector;

use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\PageBuilder\Siteaccess\Selector\Context;
use Ibexa\Contracts\PageBuilder\Siteaccess\Selector\SelectorStrategy;

final class FirstFromSiteAccessListStrategy implements SelectorStrategy
{
    private ConfigResolverInterface $configResolver;

    public function __construct(
        ConfigResolverInterface $configResolver
    ) {
        $this->configResolver = $configResolver;
    }

    public function selectSiteAccess(Context $context, array $siteAccessNameList): ?string
    {
        $siteAccessList = $this->configResolver->getParameter(
            'page_builder.siteaccess_list',
        );

        return empty($siteAccessList) ? null : reset($siteAccessList);
    }
}
