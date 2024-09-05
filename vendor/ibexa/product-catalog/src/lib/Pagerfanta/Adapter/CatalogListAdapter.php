<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Pagerfanta\Adapter;

use Ibexa\Contracts\ProductCatalog\CatalogServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\Catalog\CatalogQuery;
use Pagerfanta\Adapter\AdapterInterface;

/**
 * @implements \Pagerfanta\Adapter\AdapterInterface<\Ibexa\Contracts\ProductCatalog\Values\CatalogInterface>
 */
final class CatalogListAdapter implements AdapterInterface
{
    private CatalogServiceInterface $catalogService;

    private CatalogQuery $catalogQuery;

    public function __construct(
        CatalogServiceInterface $catalogService,
        ?CatalogQuery $catalogQuery = null
    ) {
        $this->catalogService = $catalogService;
        $this->catalogQuery = $catalogQuery ?? new CatalogQuery();
    }

    public function getNbResults(): int
    {
        $query = new CatalogQuery(
            $this->catalogQuery->getQuery(),
            $this->catalogQuery->getSortClauses(),
            0,
        );

        return $this->catalogService->findCatalogs($query)->getTotalCount();
    }

    /**
     * @return \Ibexa\Contracts\ProductCatalog\Values\Catalog\CatalogListInterface
     */
    public function getSlice($offset, $length): iterable
    {
        $query = new CatalogQuery(
            $this->catalogQuery->getQuery(),
            $this->catalogQuery->getSortClauses(),
            $length,
            $offset,
        );

        return $this->catalogService->findCatalogs($query);
    }
}
