<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Pagerfanta\Adapter;

use Ibexa\Contracts\ProductCatalog\ProductTypeServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductType\ProductTypeQuery;
use Pagerfanta\Adapter\AdapterInterface;

/**
 * @implements \Pagerfanta\Adapter\AdapterInterface<\Ibexa\Contracts\ProductCatalog\Values\ProductTypeInterface>
 */
final class ProductTypeListAdapter implements AdapterInterface
{
    private ProductTypeServiceInterface $productTypeService;

    private ProductTypeQuery $query;

    public function __construct(ProductTypeServiceInterface $productTypeService, ?ProductTypeQuery $query = null)
    {
        $this->productTypeService = $productTypeService;
        $this->query = $query ?? new ProductTypeQuery();
    }

    public function getNbResults()
    {
        return $this->productTypeService->findProductTypes(
            new ProductTypeQuery($this->query->getNamePrefix(), 0, 0)
        )->getTotalCount();
    }

    /**
     * @return \Ibexa\Contracts\ProductCatalog\Values\ProductType\ProductTypeListInterface
     */
    public function getSlice($offset, $length)
    {
        return $this->productTypeService->findProductTypes(
            new ProductTypeQuery($this->query->getNamePrefix(), $offset, $length)
        );
    }
}
