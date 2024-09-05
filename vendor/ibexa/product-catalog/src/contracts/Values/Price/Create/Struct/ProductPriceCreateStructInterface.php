<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Values\Price\Create\Struct;

use Ibexa\Contracts\ProductCatalog\Values\CurrencyInterface;
use Ibexa\Contracts\ProductCatalog\Values\Price\ProductPriceStructInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Money\Money;

interface ProductPriceCreateStructInterface extends ProductPriceStructInterface
{
    /**
     * Return a value that all identifies ProductPrice specific type.
     *
     * @phpstan-return non-empty-string
     */
    public static function getDiscriminator(): string;

    public function getProduct(): ProductInterface;

    public function getMoney(): Money;

    /**
     * @return numeric-string
     */
    public function getAmount(): string;

    public function getCurrency(): CurrencyInterface;

    public function getCustomPrice(): ?Money;

    /**
     * @return numeric-string|null
     */
    public function getCustomPriceRule(): ?string;
}
