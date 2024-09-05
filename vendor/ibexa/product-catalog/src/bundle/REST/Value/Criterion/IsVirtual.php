<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Value\Criterion;

use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\IsVirtual as IsVirtualCriterion;
use Ibexa\Rest\Value;

final class IsVirtual extends Value
{
    public IsVirtualCriterion $isVirtual;

    public function __construct(IsVirtualCriterion $isVirtual)
    {
        $this->isVirtual = $isVirtual;
    }
}
