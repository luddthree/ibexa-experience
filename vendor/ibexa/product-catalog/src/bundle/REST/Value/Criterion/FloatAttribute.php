<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Value\Criterion;

use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\FloatAttribute as FloatAttributeCriterion;
use Ibexa\Rest\Value;

final class FloatAttribute extends Value
{
    public FloatAttributeCriterion $floatAttribute;

    public function __construct(FloatAttributeCriterion $floatAttribute)
    {
        $this->floatAttribute = $floatAttribute;
    }
}
