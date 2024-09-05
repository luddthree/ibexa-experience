<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\SiteFactory;

use Ibexa\Contracts\SiteFactory\Values\Site\Page;
use Ibexa\PageBuilder\Siteaccess\SiteaccessService;

/**
 * @internal
 */
class PageDomainMapper
{
    /** @var \Ibexa\PageBuilder\Siteaccess\SiteaccessService */
    private $siteAccessService;

    public function __construct(
        SiteaccessService $siteAccessService
    ) {
        $this->siteAccessService = $siteAccessService;
    }

    public function buildPageDomainObject(string $siteAccessName): Page
    {
        $treeRootLocation = $this->siteAccessService->getRootLocation($siteAccessName);
        $languages = $this->siteAccessService->getLanguages($siteAccessName);

        return new Page($siteAccessName, $languages, $treeRootLocation);
    }

    public function buildPagesDomainObjectList(array $pagesData): array
    {
        $pages = [];
        foreach ($pagesData as $siteAccessName) {
            $pages[] = $this->buildPageDomainObject($siteAccessName);
        }

        return $pages;
    }
}

class_alias(PageDomainMapper::class, 'EzSystems\EzPlatformSiteFactory\PageDomainMapper');
