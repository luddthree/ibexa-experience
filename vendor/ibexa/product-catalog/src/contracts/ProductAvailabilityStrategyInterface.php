<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog;

use Ibexa\Contracts\ProductCatalog\Values\Availability\AvailabilityContextInterface;
use Ibexa\Contracts\ProductCatalog\Values\Availability\AvailabilityInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;

interface ProductAvailabilityStrategyInterface
{
    public function accept(AvailabilityContextInterface $context): bool;

    public function getProductAvailability(
        ProductInterface $product,
        AvailabilityContextInterface $context
    ): AvailabilityInterface;
}
