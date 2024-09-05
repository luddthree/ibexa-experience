<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\DataMapper;

use Ibexa\Bundle\ProductCatalog\Form\Data\Availability\ProductAvailabilityUpdateData;
use Ibexa\Contracts\ProductCatalog\Values\Availability\ProductAvailabilityUpdateStruct;

final class ProductAvailabilityUpdateMapper
{
    public function mapToStruct(ProductAvailabilityUpdateData $data): ProductAvailabilityUpdateStruct
    {
        $stock = $data->getStock();
        $isInfinite = $data->isInfinite();
        if ($data->isInfinite()) {
            $stock = null;
        }

        return new ProductAvailabilityUpdateStruct(
            $data->getProduct(),
            $data->isAvailable(),
            $isInfinite,
            $stock
        );
    }
}
