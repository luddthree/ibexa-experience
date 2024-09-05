<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Value\Criterion;

use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\IntegerAttribute as IntegerAttributeCriterion;
use Ibexa\Rest\Value;

final class IntegerAttribute extends Value
{
    public IntegerAttributeCriterion $integerAttribute;

    public function __construct(IntegerAttributeCriterion $integerAttribute)
    {
        $this->integerAttribute = $integerAttribute;
    }
}
