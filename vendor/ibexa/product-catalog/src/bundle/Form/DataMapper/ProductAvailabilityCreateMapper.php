<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\DataMapper;

use Ibexa\Bundle\ProductCatalog\Form\Data\Availability\ProductAvailabilityCreateData;
use Ibexa\Contracts\ProductCatalog\Values\Availability\ProductAvailabilityCreateStruct;

final class ProductAvailabilityCreateMapper
{
    public function mapToStruct(ProductAvailabilityCreateData $data): ProductAvailabilityCreateStruct
    {
        $isAvailable = $data->isAvailable();
        $isInfinite = $data->isInfinite();
        $stock = $data->getStock();
        if ($data->isInfinite()) {
            $stock = null;
        }
        assert(isset($isAvailable), 'Should be handled by form validation');

        return new ProductAvailabilityCreateStruct(
            $data->getProduct(),
            $isAvailable,
            $isInfinite,
            $stock
        );
    }
}
