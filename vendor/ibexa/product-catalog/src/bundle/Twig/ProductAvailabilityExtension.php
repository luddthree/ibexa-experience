<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class ProductAvailabilityExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'ibexa_has_product_availability',
                [ProductAvailabilityRuntime::class, 'hasProductAvailability']
            ),
            new TwigFunction(
                'ibexa_get_product_availability',
                [ProductAvailabilityRuntime::class, 'getProductAvailability']
            ),
            new TwigFunction(
                'ibexa_is_product_available',
                [ProductAvailabilityRuntime::class, 'isProductAvailable']
            ),
            new TwigFunction(
                'ibexa_get_product_stock',
                [ProductAvailabilityRuntime::class, 'getProductStock']
            ),
        ];
    }
}
