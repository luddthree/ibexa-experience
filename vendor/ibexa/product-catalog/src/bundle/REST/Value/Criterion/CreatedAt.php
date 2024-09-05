<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Value\Criterion;

use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\CreatedAt as CreatedAtCriterion;
use Ibexa\Rest\Value;

final class CreatedAt extends Value
{
    public CreatedAtCriterion $createdAt;

    public function __construct(CreatedAtCriterion $createdAt)
    {
        $this->createdAt = $createdAt;
    }
}
