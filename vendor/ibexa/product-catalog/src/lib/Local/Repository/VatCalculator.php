<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository;

use Ibexa\Contracts\ProductCatalog\Values\PriceInterface;
use Ibexa\Contracts\ProductCatalog\Values\VatCategoryInterface;
use Ibexa\Contracts\ProductCatalog\VatCalculatorInterface;
use Money\Money;

final class VatCalculator implements VatCalculatorInterface
{
    public function calculate(PriceInterface $price, VatCategoryInterface $vatCategory): Money
    {
        return $price->getMoney()->multiply(
            (string)($vatCategory->getVatValue() / 100.0),
            Money::ROUND_HALF_DOWN
        );
    }
}
