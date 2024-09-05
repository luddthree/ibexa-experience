<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion;

use Ibexa\Contracts\ProductCatalog\Values\Product\Query\CriterionInterface;
use Money\Currency;
use Money\Money;

abstract class AbstractPriceCriterion implements CriterionInterface
{
    protected Money $value;

    protected string $operator;

    public function __construct(Money $value, string $operator = Operator::EQ)
    {
        $this->value = $value;
        $this->operator = $operator;
    }

    public function getValue(): Money
    {
        return $this->value;
    }

    public function getOperator(): string
    {
        return $this->operator;
    }

    public function getCurrency(): Currency
    {
        return $this->value->getCurrency();
    }
}
