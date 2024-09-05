<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\PageBuilder\Siteaccess\Selector;

use Ibexa\AdminUi\Specification\Location\IsRoot;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\PageBuilder\Siteaccess\Selector\Context;
use Ibexa\Contracts\PageBuilder\Siteaccess\Selector\SelectorStrategy;
use Ibexa\Contracts\PageBuilder\Siteaccess\SiteaccessServiceInterface as PageBuilderSiteAccessServiceInterface;

final class TreeRootStrategy implements SelectorStrategy
{
    private PageBuilderSiteAccessServiceInterface $pageBuilderSiteAccessService;

    public function __construct(
        PageBuilderSiteAccessServiceInterface $pageBuilderSiteAccessService
    ) {
        $this->pageBuilderSiteAccessService = $pageBuilderSiteAccessService;
    }

    public function selectSiteAccess(Context $context, array $siteAccessNameList): ?string
    {
        $location = $context->location;
        if ($location === null) {
            return null;
        }

        $siteAccessTreeRootLocations = [];
        foreach ($siteAccessNameList as $siteAccess) {
            $siteAccessTreeRootLocations[$siteAccess] = $this->pageBuilderSiteAccessService->getRootLocation($siteAccess);
        }

        $nonGlobalSiteAccessTreeRootLocations = array_filter(
            $siteAccessTreeRootLocations,
            static fn (?Location $location): bool => $location !== null && !(new IsRoot())->isSatisfiedBy($location)
        );

        uasort(
            $nonGlobalSiteAccessTreeRootLocations,
            static fn (Location $a, Location $b): int => $a->depth <=> $b->depth
        );

        $siteAccessToReturn = null;
        $currentMaxDepth = null;
        foreach ($nonGlobalSiteAccessTreeRootLocations as $siteAccessName => $entryCustomerPortalsLocation) {
            if ($currentMaxDepth > $entryCustomerPortalsLocation->depth) {
                return $siteAccessToReturn;
            }
            //Non-strict on purpose, path elements are strings.
            if (in_array($entryCustomerPortalsLocation->id, $location->path)) {
                $currentMaxDepth = $entryCustomerPortalsLocation->depth;
                $siteAccessToReturn = $siteAccessName;
            }
        }

        return $siteAccessToReturn;
    }
}
