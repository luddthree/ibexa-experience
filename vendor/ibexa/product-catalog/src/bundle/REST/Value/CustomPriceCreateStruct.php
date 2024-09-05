<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Value;

use Ibexa\Contracts\ProductCatalog\Values\CurrencyInterface;
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroupInterface;
use Ibexa\Contracts\ProductCatalog\Values\Price\Create\Struct\AbstractPriceCreateStruct as PriceCreateStruct;
use Ibexa\Contracts\ProductCatalog\Values\Price\Create\Struct\CustomerGroupPriceCreateStruct;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Money\Money;

final class CustomPriceCreateStruct extends AbstractPriceCreateStruct
{
    public CustomerGroupInterface $customerGroup;

    public CurrencyInterface $currency;

    public Money $money;

    public Money $customMoney;

    public function __construct(
        CustomerGroupInterface $customerGroup,
        CurrencyInterface $currency,
        Money $money,
        Money $customMoney
    ) {
        parent::__construct($currency, $money);

        $this->customerGroup = $customerGroup;
        $this->customMoney = $customMoney;
    }

    public function toCreateStruct(ProductInterface $product): PriceCreateStruct
    {
        return new CustomerGroupPriceCreateStruct(
            $this->customerGroup,
            $product,
            $this->currency,
            $this->money,
            $this->customMoney,
            null
        );
    }
}
