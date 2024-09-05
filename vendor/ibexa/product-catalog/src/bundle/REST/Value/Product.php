<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Value;

use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Ibexa\Rest\Value;

final class Product extends Value
{
    public ProductInterface $product;

    public function __construct(ProductInterface $product)
    {
        $this->product = $product;
    }
}
