<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Iterator\BatchIteratorAdapter;

use Ibexa\Contracts\Core\Repository\Iterator\BatchIteratorAdapter;
use Ibexa\Contracts\ProductCatalog\ProductTypeServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductType\ProductTypeQuery;
use Iterator;

final class ProductTypeListAdapter implements BatchIteratorAdapter
{
    private ProductTypeServiceInterface $productTypeService;

    public function __construct(ProductTypeServiceInterface $productTypeService)
    {
        $this->productTypeService = $productTypeService;
    }

    /**
     * @return \Iterator<\Ibexa\Contracts\ProductCatalog\Values\ProductTypeInterface>
     */
    public function fetch(int $offset, int $limit): Iterator
    {
        return $this->productTypeService->findProductTypes(
            new ProductTypeQuery(null, $offset, $limit)
        )->getIterator();
    }
}
