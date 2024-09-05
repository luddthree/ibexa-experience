<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Value\Criterion;

use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\ProductType as ProductTypeCriterion;
use Ibexa\Rest\Value;

final class ProductType extends Value
{
    public ProductTypeCriterion $productType;

    public function __construct(ProductTypeCriterion $productType)
    {
        $this->productType = $productType;
    }
}
