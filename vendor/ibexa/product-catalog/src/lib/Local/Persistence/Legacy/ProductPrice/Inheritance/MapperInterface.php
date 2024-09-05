<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Legacy\ProductPrice\Inheritance;

use Ibexa\ProductCatalog\Local\Persistence\Values\AbstractProductPrice;
use Ibexa\ProductCatalog\Local\Persistence\Values\Currency;

/**
 * Maps database result set into SPI objects when using Class-Table inheritance.
 */
interface MapperInterface
{
    public function canHandleResultSet(string $discriminator): bool;

    /**
     * @phpstan-param array{
     *   id: int,
     *   amount: numeric-string,
     *   custom_price_amount: numeric-string|null,
     *   custom_price_rule: numeric-string|null,
     *   currency_id: int,
     *   product_code: non-empty-string,
     *   discriminator: string,
     * } $row
     */
    public function handleResultSet(string $discriminator, array $row, Currency $currency): AbstractProductPrice;
}
