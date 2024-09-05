<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Value\Criterion;

use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\CreatedAtRange as CreatedAtRangeCriterion;
use Ibexa\Rest\Value;

final class CreatedAtRange extends Value
{
    public CreatedAtRangeCriterion $createdAtRange;

    public function __construct(CreatedAtRangeCriterion $createdAtRange)
    {
        $this->createdAtRange = $createdAtRange;
    }
}
