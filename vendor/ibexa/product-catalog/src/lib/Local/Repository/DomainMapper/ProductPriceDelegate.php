<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\DomainMapper;

use Ibexa\Contracts\ProductCatalog\PriceResolverInterface;
use Ibexa\Contracts\ProductCatalog\Values\Price\PriceContextInterface;
use Ibexa\Contracts\ProductCatalog\Values\PriceInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;

/**
 * @final
 */
class ProductPriceDelegate
{
    private PriceResolverInterface $priceResolver;

    public function __construct(PriceResolverInterface $priceResolver)
    {
        $this->priceResolver = $priceResolver;
    }

    public function getPrice(
        ProductInterface $product,
        ?PriceContextInterface $context = null
    ): ?PriceInterface {
        return $this->priceResolver->resolvePrice($product, $context);
    }
}
