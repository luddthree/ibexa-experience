<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Value\Criterion;

use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\ProductCategory as ProductCategoryCriterion;
use Ibexa\Rest\Value;

final class ProductCategory extends Value
{
    public ProductCategoryCriterion $productCategory;

    public function __construct(ProductCategoryCriterion $productCategory)
    {
        $this->productCategory = $productCategory;
    }
}
