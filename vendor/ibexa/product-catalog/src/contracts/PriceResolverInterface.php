<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog;

use Ibexa\Contracts\Core\Collection\MapInterface;
use Ibexa\Contracts\ProductCatalog\Values\Price\PriceContextInterface;
use Ibexa\Contracts\ProductCatalog\Values\PriceInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;

interface PriceResolverInterface
{
    public function resolvePrice(
        ProductInterface $product,
        ?PriceContextInterface $context = null
    ): ?PriceInterface;

    /**
     * @param array<\Ibexa\Contracts\ProductCatalog\Values\ProductInterface> $products
     *
     * @return MapInterface<\Ibexa\Contracts\ProductCatalog\Values\ProductInterface, ?\Ibexa\Contracts\ProductCatalog\Values\PriceInterface>
     */
    public function resolvePrices(
        array $products,
        ?PriceContextInterface $context = null
    ): MapInterface;
}
