<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\Values;

use Ibexa\Contracts\ProductCatalog\Values\CurrencyInterface;
use Ibexa\Contracts\ProductCatalog\Values\PriceInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Money\Money;
use Money\MoneyFormatter;
use Stringable;

abstract class AbstractPrice implements PriceInterface, Stringable
{
    protected int $id;

    protected MoneyFormatter $moneyFormatter;

    protected ProductInterface $product;

    protected CurrencyInterface $currency;

    protected Money $money;

    public function __construct(
        MoneyFormatter $moneyFormatter,
        int $id,
        ProductInterface $product,
        CurrencyInterface $currency,
        Money $money
    ) {
        $this->moneyFormatter = $moneyFormatter;
        $this->product = $product;
        $this->currency = $currency;
        $this->money = $money;
        $this->id = $id;
    }

    public function getId(): int
    {
        return $this->id;
    }

    final public function getProduct(): ProductInterface
    {
        return $this->product;
    }

    public function getMoney(): Money
    {
        return $this->getBaseMoney();
    }

    public function getBaseMoney(): Money
    {
        return $this->money;
    }

    public function getAmount(): string
    {
        return $this->getBaseAmount();
    }

    public function getBaseAmount(): string
    {
        return $this->moneyFormatter->format($this->money);
    }

    final public function getCurrency(): CurrencyInterface
    {
        return $this->currency;
    }

    final public function __toString(): string
    {
        return $this->getAmount() . ' ' . $this->getMoney()->getCurrency()->getCode();
    }
}
