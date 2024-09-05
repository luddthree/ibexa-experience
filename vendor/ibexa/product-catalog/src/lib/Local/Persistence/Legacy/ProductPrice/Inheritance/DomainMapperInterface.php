<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Legacy\ProductPrice\Inheritance;

use Ibexa\Contracts\ProductCatalog\Values\CurrencyInterface;
use Ibexa\Contracts\ProductCatalog\Values\PriceInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Ibexa\ProductCatalog\Local\Persistence\Values\AbstractProductPrice;
use Money\Money;
use Money\MoneyFormatter;
use Money\MoneyParser;

/**
 * Instantiates API objects when using Class-Table inherited data.
 */
interface DomainMapperInterface
{
    public function mapSpiPrice(
        MoneyFormatter $moneyFormatter,
        MoneyParser $moneyParser,
        ProductInterface $product,
        CurrencyInterface $currency,
        AbstractProductPrice $spiPrice,
        Money $money
    ): PriceInterface;

    public function canMapSpiPrice(AbstractProductPrice $spiPrice): bool;
}
