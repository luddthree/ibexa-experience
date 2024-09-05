<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Legacy\ProductPrice;

use Ibexa\Contracts\ProductCatalog\Values\CurrencyInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\ProductPrice\Inheritance\DomainMapperInterface;
use Ibexa\ProductCatalog\Local\Persistence\Values\AbstractProductPrice;
use Ibexa\ProductCatalog\Local\Persistence\Values\ProductPrice;
use Ibexa\ProductCatalog\Local\Repository\Values\Price;
use Money\Money;
use Money\MoneyFormatter;
use Money\MoneyParser;

final class BasicDomainMapper implements DomainMapperInterface
{
    public function canMapSpiPrice(AbstractProductPrice $spiPrice): bool
    {
        return $spiPrice instanceof ProductPrice;
    }

    public function mapSpiPrice(
        MoneyFormatter $moneyFormatter,
        MoneyParser $moneyParser,
        ProductInterface $product,
        CurrencyInterface $currency,
        AbstractProductPrice $spiPrice,
        Money $money
    ): Price {
        return new Price(
            $moneyFormatter,
            $spiPrice->getId(),
            $product,
            $currency,
            $money,
        );
    }
}
