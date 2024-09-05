<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Legacy\ProductPrice;

use Ibexa\Contracts\ProductCatalog\Values\Price\Create\Struct\ProductPriceCreateStruct;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\ProductPrice\Inheritance\MapperInterface;
use Ibexa\ProductCatalog\Local\Persistence\Values\Currency;
use Ibexa\ProductCatalog\Local\Persistence\Values\ProductPrice;

final class BasicMapper implements MapperInterface
{
    public function canHandleResultSet(string $discriminator): bool
    {
        return $discriminator === ProductPriceCreateStruct::getDiscriminator();
    }

    public function handleResultSet(string $discriminator, array $row, Currency $currency): ProductPrice
    {
        return new ProductPrice(
            $row['id'],
            $row['amount'],
            $currency,
            $row['product_code'],
        );
    }
}
