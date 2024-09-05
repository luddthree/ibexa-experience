<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Value;

use Ibexa\Contracts\ProductCatalog\Values\CurrencyInterface;
use Ibexa\Contracts\ProductCatalog\Values\Price\Create\Struct\AbstractPriceCreateStruct as APIPriceCreateStruct;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Ibexa\Rest\Value;
use Money\Money;

abstract class AbstractPriceCreateStruct extends Value
{
    public CurrencyInterface $currency;

    public Money $money;

    public function __construct(CurrencyInterface $currency, Money $money)
    {
        $this->currency = $currency;
        $this->money = $money;
    }

    abstract public function toCreateStruct(ProductInterface $product): APIPriceCreateStruct;
}
