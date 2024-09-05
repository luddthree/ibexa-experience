<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Value;

use Ibexa\Contracts\ProductCatalog\Values\Price\Update\Struct\ProductPriceUpdateStruct;
use Ibexa\Contracts\ProductCatalog\Values\Price\Update\Struct\ProductPriceUpdateStructInterface as PriceUpdateStructInterface;
use Ibexa\Contracts\ProductCatalog\Values\PriceInterface;

final class BasePriceUpdateStruct extends AbstractPriceUpdateStruct
{
    public function toUpdateStruct(PriceInterface $price): PriceUpdateStructInterface
    {
        return new ProductPriceUpdateStruct($price, $this->money);
    }
}
