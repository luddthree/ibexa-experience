<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Value\Criterion;

use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\IntegerAttributeRange as IntegerAttributeRangeCriterion;
use Ibexa\Rest\Value;

final class IntegerAttributeRange extends Value
{
    public IntegerAttributeRangeCriterion $integerAttributeRange;

    public function __construct(IntegerAttributeRangeCriterion $integerAttributeRange)
    {
        $this->integerAttributeRange = $integerAttributeRange;
    }
}
