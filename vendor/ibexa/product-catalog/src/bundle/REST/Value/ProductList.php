<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Value;

use Ibexa\Rest\Value;

final class ProductList extends Value
{
    /**
     * @var \Ibexa\Bundle\ProductCatalog\REST\Value\Product[]
     */
    public array $products = [];

    /**
     * @param \Ibexa\Bundle\ProductCatalog\REST\Value\Product[] $products
     */
    public function __construct(array $products)
    {
        $this->products = $products;
    }
}
