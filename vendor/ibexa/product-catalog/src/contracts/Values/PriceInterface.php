<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Values;

use Money\Money;

interface PriceInterface
{
    public function getId(): int;

    public function getProduct(): ProductInterface;

    /**
     * @return numeric-string
     */
    public function getAmount(): string;

    /**
     * @return numeric-string
     */
    public function getBaseAmount(): string;

    public function getCurrency(): CurrencyInterface;

    public function getMoney(): Money;

    public function getBaseMoney(): Money;

    public function __toString(): string;
}
