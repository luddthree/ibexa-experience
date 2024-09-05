<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Values\Price;

use Ibexa\Contracts\ProductCatalog\Values\CurrencyInterface;
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroupInterface;

interface PriceContextInterface
{
    public function getCurrency(): ?CurrencyInterface;

    public function setCurrency(?CurrencyInterface $currency): void;

    public function getCustomerGroup(): ?CustomerGroupInterface;

    public function setCustomerGroup(?CustomerGroupInterface $customerGroup): void;
}
