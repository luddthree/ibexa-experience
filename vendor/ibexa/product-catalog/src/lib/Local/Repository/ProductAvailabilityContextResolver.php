<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository;

use Ibexa\Contracts\ProductCatalog\ProductAvailabilityContextResolverInterface;
use Ibexa\Contracts\ProductCatalog\Values\Availability\AvailabilityContextInterface;
use Ibexa\ProductCatalog\Local\Repository\Values\AvailabilityContext\AvailabilityContext;

final class ProductAvailabilityContextResolver implements ProductAvailabilityContextResolverInterface
{
    public function resolve(
        ?AvailabilityContextInterface $context = null
    ): AvailabilityContextInterface {
        return $context ?? new AvailabilityContext();
    }
}
