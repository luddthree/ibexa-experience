<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog;

use Ibexa\Contracts\ProductCatalog\Values\PriceInterface;
use Ibexa\Contracts\ProductCatalog\Values\VatCategoryInterface;
use Money\Money;

interface VatCalculatorInterface
{
    public function calculate(PriceInterface $price, VatCategoryInterface $vatCategory): Money;
}
