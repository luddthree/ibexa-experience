<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Values\Price\Query\Criterion;

use Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\FieldValueCriterion;
use Ibexa\Contracts\ProductCatalog\Values\Price\Query\CriterionInterface;

final class Currency extends FieldValueCriterion implements CriterionInterface
{
    /**
     * @param \Ibexa\Contracts\ProductCatalog\Values\CurrencyInterface|\Ibexa\Contracts\ProductCatalog\Values\CurrencyInterface[] $value
     */
    public function __construct($value)
    {
        parent::__construct('currency_id', $value);
    }
}
