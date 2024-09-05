<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Values\CustomerGroup\Query\Criterion;

use Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\FieldValueCriterion;

final class CustomerGroupName extends FieldValueCriterion
{
    public function __construct(string $name, string $operator = FieldValueCriterion::COMPARISON_EQ)
    {
        parent::__construct('name', $name, $operator);
    }
}
