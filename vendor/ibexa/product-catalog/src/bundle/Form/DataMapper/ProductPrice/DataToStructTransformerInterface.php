<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\DataMapper\ProductPrice;

use Ibexa\Bundle\ProductCatalog\Form\Data\Price\ProductPriceDataInterface;
use Ibexa\Contracts\ProductCatalog\Values\Price\ProductPriceStructInterface;

interface DataToStructTransformerInterface
{
    public function convertDataToStruct(ProductPriceDataInterface $priceData): ProductPriceStructInterface;

    public function supports(ProductPriceDataInterface $priceData): bool;
}
