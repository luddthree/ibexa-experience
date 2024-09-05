<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Pagerfanta\Adapter;

use Ibexa\Contracts\ProductCatalog\ProductServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\Product\ProductVariantListInterface;
use Ibexa\Contracts\ProductCatalog\Values\Product\ProductVariantQuery;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Pagerfanta\Adapter\AdapterInterface;

/**
 * @implements \Pagerfanta\Adapter\AdapterInterface<\Ibexa\Contracts\ProductCatalog\Values\ProductInterface>
 */
final class ProductVariantListAdapter implements AdapterInterface
{
    private ProductServiceInterface $productService;

    private ProductInterface $product;

    public function __construct(
        ProductServiceInterface $productService,
        ProductInterface $product
    ) {
        $this->productService = $productService;
        $this->product = $product;
    }

    public function getNbResults(): int
    {
        return $this->productService->findProductVariants(
            $this->product,
            new ProductVariantQuery(0, 0)
        )->getTotalCount();
    }

    /**
     * @return \Ibexa\Contracts\ProductCatalog\Values\Product\ProductVariantListInterface
     */
    public function getSlice($offset, $length): ProductVariantListInterface
    {
        return $this->productService->findProductVariants(
            $this->product,
            new ProductVariantQuery($offset, $length)
        );
    }
}
