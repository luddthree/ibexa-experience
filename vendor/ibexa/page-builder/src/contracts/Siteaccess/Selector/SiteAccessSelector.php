<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\PageBuilder\Siteaccess\Selector;

interface SiteAccessSelector
{
    /**
     * @param string[] $siteAccessNameList
     */
    public function selectSiteAccess(Context $context, array $siteAccessNameList): string;
}
