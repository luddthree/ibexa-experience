<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\PageBuilder\Siteaccess;

use Ibexa\AdminUi\Siteaccess\SiteaccessResolverInterface;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;

final class PageBuilderSiteAccessResolver implements SiteaccessResolverInterface
{
    /** @var iterable<\Ibexa\Contracts\PageBuilder\Siteaccess\Resolver\ListResolverStrategyInterface> */
    private iterable $resolverStrategies;

    /**
     * @param iterable<\Ibexa\Contracts\PageBuilder\Siteaccess\Resolver\ListResolverStrategyInterface>  $resolverStrategies
     */
    public function __construct(iterable $resolverStrategies)
    {
        $this->resolverStrategies = $resolverStrategies;
    }

    public function getSiteaccessesForLocation(
        Location $location,
        int $versionNo = null,
        string $languageCode = null
    ): array {
        return array_column($this->getSiteAccessesListForLocation($location, $versionNo, $languageCode), 'name');
    }

    public function getSiteAccessesListForLocation(
        Location $location,
        ?int $versionNo = null,
        ?string $languageCode = null
    ): array {
        foreach ($this->resolverStrategies as $strategy) {
            $siteAccessList = $strategy->getSiteAccessListForLocation($location, $versionNo, $languageCode);

            if ($siteAccessList !== null) {
                return $siteAccessList;
            }
        }

        return [];
    }

    public function getSiteAccessesListForContent(Content $content): array
    {
        foreach ($this->resolverStrategies as $strategy) {
            $siteAccessList = $strategy->getSiteAccessListForContent($content);

            if ($siteAccessList !== null) {
                return $siteAccessList;
            }
        }

        return [];
    }

    public function getSiteAccessesList(): array
    {
        foreach ($this->resolverStrategies as $strategy) {
            $siteAccessList = $strategy->getSiteAccessList();

            if ($siteAccessList !== null) {
                return $siteAccessList;
            }
        }

        return [];
    }

    public function getSiteaccesses(): array
    {
        return array_column(
            $this->getSiteAccessesList(),
            'name'
        );
    }
}
