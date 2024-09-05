<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Values\Region\Query\Criterion;

use Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\FieldValueCriterion;

final class RegionIdentifierCriterion extends FieldValueCriterion
{
    public function __construct($value, string $operator = self::COMPARISON_EQ)
    {
        parent::__construct('identifier', $value, $operator);
    }
}
