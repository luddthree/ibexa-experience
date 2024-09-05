<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\DataMapper\ProductPrice\Delete;

use Ibexa\Bundle\ProductCatalog\Form\Data\Price\ProductPriceDataInterface;
use Ibexa\Bundle\ProductCatalog\Form\Data\Price\Update\CustomerGroupPriceUpdateData;
use Ibexa\Bundle\ProductCatalog\Form\Data\Price\Update\ProductPriceUpdateData;
use Ibexa\Bundle\ProductCatalog\Form\DataMapper\ProductPrice\DataToStructTransformerInterface;
use Ibexa\Contracts\ProductCatalog\Values\Price\Delete\Struct\ProductPriceDeleteStruct;
use Ibexa\Contracts\ProductCatalog\Values\Price\ProductPriceStructInterface;

final class BaseDataToStructTransformer implements DataToStructTransformerInterface
{
    public function convertDataToStruct(ProductPriceDataInterface $priceData): ProductPriceStructInterface
    {
        assert(
            $priceData instanceof ProductPriceUpdateData ||
            $priceData instanceof CustomerGroupPriceUpdateData
        );

        return new ProductPriceDeleteStruct($priceData->getPrice());
    }

    public function supports(ProductPriceDataInterface $priceData): bool
    {
        if (
            !$priceData instanceof ProductPriceUpdateData &&
            !$priceData instanceof CustomerGroupPriceUpdateData
        ) {
            return false;
        }

        return $priceData->getBasePrice() === null && $priceData->getCustomPrice() === null;
    }
}
