<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Value;

use Ibexa\Contracts\ProductCatalog\Values\Price\Create\Struct\AbstractPriceCreateStruct as PriceCreateStruct;
use Ibexa\Contracts\ProductCatalog\Values\Price\Create\Struct\ProductPriceCreateStruct;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;

final class BasePriceCreateStruct extends AbstractPriceCreateStruct
{
    public function toCreateStruct(ProductInterface $product): PriceCreateStruct
    {
        return new ProductPriceCreateStruct($product, $this->currency, $this->money);
    }
}
