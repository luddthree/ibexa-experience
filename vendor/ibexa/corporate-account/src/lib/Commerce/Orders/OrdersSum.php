<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Commerce\Orders;

use Money\Currency;
use Money\Money;

final class OrdersSum
{
    private float $sum;

    private string $currency;

    private int $ordersCount;

    public function __construct(float $sum, string $currency, int $ordersCount)
    {
        $this->sum = $sum;
        $this->currency = $currency;
        $this->ordersCount = $ordersCount;
    }

    public function getSum(): float
    {
        return $this->sum;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function getOrdersCount(): int
    {
        return $this->ordersCount;
    }

    public function getAverage(): float
    {
        return $this->ordersCount <= 0 ? 0.0 : $this->sum / $this->ordersCount;
    }

    public function getSumMoney(): ?Money
    {
        if ($this->getMoneyCurrency() === null) {
            return null;
        }

        return new Money(
            (string)$this->sum,
            $this->getMoneyCurrency()
        );
    }

    public function getAverageMoney(): ?Money
    {
        $moneyCurrency = $this->getMoneyCurrency();
        $sumMoney = $this->getSumMoney();

        if ($moneyCurrency === null || $sumMoney === null) {
            return null;
        }

        if ($sumMoney->isZero()) {
            return new Money(0, $moneyCurrency);
        }

        return $sumMoney->divide($this->ordersCount);
    }

    private function getMoneyCurrency(): ?Currency
    {
        if (empty($this->currency)) {
            return null;
        }

        return new Currency($this->currency);
    }
}
