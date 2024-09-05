<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Value;

use Ibexa\Contracts\ProductCatalog\Values\Price\Update\Struct\ProductPriceUpdateStructInterface as PriceUpdateStructInterface;
use Ibexa\Contracts\ProductCatalog\Values\PriceInterface;
use Ibexa\Rest\Value;
use Money\Money;

abstract class AbstractPriceUpdateStruct extends Value
{
    public ?Money $money;

    public function __construct(?Money $money)
    {
        $this->money = $money;
    }

    abstract public function toUpdateStruct(PriceInterface $price): PriceUpdateStructInterface;
}
