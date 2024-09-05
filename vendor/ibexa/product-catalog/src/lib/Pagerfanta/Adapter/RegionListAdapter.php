<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Pagerfanta\Adapter;

use Ibexa\Contracts\ProductCatalog\RegionServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\Region\RegionQuery;
use Pagerfanta\Adapter\AdapterInterface;

/**
 * @implements \Pagerfanta\Adapter\AdapterInterface<\Ibexa\Contracts\ProductCatalog\Values\RegionInterface>
 */
final class RegionListAdapter implements AdapterInterface
{
    private RegionServiceInterface $regionService;

    private RegionQuery $query;

    public function __construct(RegionServiceInterface $regionService, ?RegionQuery $query = null)
    {
        $this->regionService = $regionService;
        $this->query = $query ?? new RegionQuery();
    }

    public function getNbResults(): int
    {
        $query = new RegionQuery(
            $this->query->getQuery(),
            $this->query->getSortClauses(),
            0,
        );

        return $this->regionService->findRegions($query)->getTotalCount();
    }

    public function getSlice($offset, $length): iterable
    {
        $query = new RegionQuery(
            $this->query->getQuery(),
            $this->query->getSortClauses(),
            $length,
            $offset,
        );

        return $this->regionService->findRegions($query);
    }
}
