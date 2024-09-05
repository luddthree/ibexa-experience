<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Value\Criterion;

use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\FloatAttributeRange as FloatAttributeRangeCriterion;
use Ibexa\Rest\Value;

final class FloatAttributeRange extends Value
{
    public FloatAttributeRangeCriterion $floatAttributeRange;

    public function __construct(FloatAttributeRangeCriterion $floatAttributeRange)
    {
        $this->floatAttributeRange = $floatAttributeRange;
    }
}
