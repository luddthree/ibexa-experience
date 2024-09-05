<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Pagerfanta\Adapter;

use Ibexa\Contracts\ProductCatalog\ProductServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\Product\ProductQuery;
use Pagerfanta\Adapter\AdapterInterface;

/**
 * @implements \Pagerfanta\Adapter\AdapterInterface<\Ibexa\Contracts\ProductCatalog\Values\ProductInterface>
 */
final class ProductListAdapter implements AdapterInterface
{
    private ProductServiceInterface $productService;

    private ProductQuery $query;

    public function __construct(ProductServiceInterface $productService, ?ProductQuery $query = null)
    {
        $this->productService = $productService;
        $this->query = $query ?? new ProductQuery();
    }

    public function getNbResults(): int
    {
        $productQuery = new ProductQuery(
            $this->query->getFilter(),
            $this->query->getQuery(),
            $this->query->getSortClauses(),
            0,
            0
        );

        return $this->productService->findProducts($productQuery)->getTotalCount();
    }

    /**
     * @return \Ibexa\Contracts\ProductCatalog\Values\Product\ProductListInterface
     */
    public function getSlice($offset, $length): iterable
    {
        $productQuery = new ProductQuery(
            $this->query->getFilter(),
            $this->query->getQuery(),
            $this->query->getSortClauses(),
            $offset,
            $length
        );

        return $this->productService->findProducts($productQuery);
    }
}
