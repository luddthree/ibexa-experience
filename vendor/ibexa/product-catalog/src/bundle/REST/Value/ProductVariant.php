<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Value;

use Ibexa\Contracts\ProductCatalog\Values\ProductVariantInterface;
use Ibexa\Rest\Value;

final class ProductVariant extends Value
{
    public ProductVariantInterface $productVariant;

    public function __construct(ProductVariantInterface $productVariant)
    {
        $this->productVariant = $productVariant;
    }
}
