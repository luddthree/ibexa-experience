<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Iterator\BatchIteratorAdapter;

use Ibexa\Contracts\Core\Repository\Iterator\BatchIteratorAdapter;
use Ibexa\Contracts\ProductCatalog\ProductServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\Product\ProductVariantQuery;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Iterator;

final class VariantFetchAdapter implements BatchIteratorAdapter
{
    private ProductServiceInterface $productService;

    private ProductVariantQuery $query;

    private ProductInterface $product;

    public function __construct(
        ProductServiceInterface $productService,
        ProductInterface $product,
        ?ProductVariantQuery $query = null
    ) {
        $this->productService = $productService;
        $this->query = $query ?? new ProductVariantQuery();
        $this->product = $product;
    }

    /**
     * @return \Iterator<\Ibexa\Contracts\ProductCatalog\Values\ProductVariantInterface>
     */
    public function fetch(int $offset, int $limit): Iterator
    {
        $query = clone $this->query;
        $query->setOffset($offset);
        $query->setLimit($limit);

        /** @var \ArrayIterator<int,\Ibexa\Contracts\ProductCatalog\Values\ProductVariantInterface> */
        return $this->productService->findProductVariants($this->product, $query)->getIterator();
    }
}
