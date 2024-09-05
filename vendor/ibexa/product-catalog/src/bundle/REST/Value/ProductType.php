<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Value;

use Ibexa\Contracts\ProductCatalog\Values\ProductTypeInterface;
use Ibexa\Rest\Value;

final class ProductType extends Value
{
    public ProductTypeInterface $productType;

    public function __construct(ProductTypeInterface $productType)
    {
        $this->productType = $productType;
    }
}
