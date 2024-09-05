<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\Values\Price;

use Ibexa\Contracts\ProductCatalog\Values\CurrencyInterface;
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroupInterface;
use Ibexa\Contracts\ProductCatalog\Values\CustomPriceAwareInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Ibexa\ProductCatalog\Local\Repository\Values\AbstractPrice;
use Money\Money;
use Money\MoneyFormatter;

final class CustomGroupPrice extends AbstractPrice implements CustomPriceAwareInterface
{
    private CustomerGroupInterface $customerGroup;

    private ?Money $customPriceMoney;

    /**
     * @var numeric-string|null
     */
    private ?string $customPriceRule;

    /**
     * @param numeric-string|null $customPriceRule
     */
    public function __construct(
        MoneyFormatter $moneyFormatter,
        int $id,
        ProductInterface $product,
        CurrencyInterface $currency,
        Money $money,
        ?Money $customPriceMoney,
        ?string $customPriceRule,
        CustomerGroupInterface $customerGroup
    ) {
        parent::__construct($moneyFormatter, $id, $product, $currency, $money);

        $this->customPriceMoney = $customPriceMoney;
        $this->customPriceRule = $customPriceRule;
        $this->customerGroup = $customerGroup;
    }

    public function getCustomerGroup(): CustomerGroupInterface
    {
        return $this->customerGroup;
    }

    public function getMoney(): Money
    {
        return $this->getCustomPrice() ?? $this->getBaseMoney();
    }

    public function getAmount(): string
    {
        return $this->getCustomPriceAmount() ?? parent::getAmount();
    }

    public function getCustomPrice(): ?Money
    {
        return $this->customPriceMoney;
    }

    public function getCustomPriceAmount(): ?string
    {
        if ($this->customPriceMoney === null) {
            return null;
        }

        return $this->moneyFormatter->format($this->customPriceMoney);
    }

    public function getGlobalPriceRate(): string
    {
        return $this->customerGroup->getGlobalPriceRate();
    }

    /**
     * @return numeric-string|null
     */
    public function getCustomPriceRule(): ?string
    {
        return $this->customPriceRule;
    }
}
