<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Values\Product\ProductList\AggregationResult;

use Ibexa\Contracts\Core\Repository\Values\Content\Search\AggregationResult;
use Money\Money;

final class PriceStatsAggregationResult extends AggregationResult
{
    public ?Money $sum;

    private ?int $count;

    private ?Money $min;

    private ?Money$max;

    private ?Money $avg;

    public function __construct(string $name, ?int $count, ?Money $min, ?Money $max, ?Money $avg, ?Money $sum)
    {
        parent::__construct($name);

        $this->count = $count;
        $this->min = $min;
        $this->max = $max;
        $this->avg = $avg;
        $this->sum = $sum;
    }

    public function getCount(): ?int
    {
        return $this->count;
    }

    public function getMin(): ?Money
    {
        return $this->min;
    }

    public function getMax(): ?Money
    {
        return $this->max;
    }

    public function getAvg(): ?Money
    {
        return $this->avg;
    }

    public function getSum(): ?Money
    {
        return $this->sum;
    }
}
