<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Value\Criterion;

use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\ProductName as ProductNameCriterion;
use Ibexa\Rest\Value;

final class ProductName extends Value
{
    public ProductNameCriterion $productName;

    public function __construct(ProductNameCriterion $productName)
    {
        $this->productName = $productName;
    }
}
