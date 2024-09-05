<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Value\Criterion;

use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\ProductAvailability as ProductAvailabilityCriterion;
use Ibexa\Rest\Value;

final class ProductAvailability extends Value
{
    public ProductAvailabilityCriterion $productAvailability;

    public function __construct(ProductAvailabilityCriterion $productAvailability)
    {
        $this->productAvailability = $productAvailability;
    }
}
