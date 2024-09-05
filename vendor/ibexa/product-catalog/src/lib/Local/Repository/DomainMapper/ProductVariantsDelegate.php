<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\DomainMapper;

use Ibexa\Contracts\ProductCatalog\ProductServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\Product\ProductVariantListInterface;
use Ibexa\Contracts\ProductCatalog\Values\Product\ProductVariantQuery;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;

/**
 * @final
 */
class ProductVariantsDelegate
{
    private ProductServiceInterface $productService;

    public function __construct(ProductServiceInterface $productService)
    {
        $this->productService = $productService;
    }

    public function getVariants(
        ProductInterface $product,
        ?ProductVariantQuery $query = null
    ): ProductVariantListInterface {
        return $this->productService->findProductVariants($product, $query);
    }
}
