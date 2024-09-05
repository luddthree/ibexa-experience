<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Twig;

use Ibexa\Contracts\ProductCatalog\ProductAvailabilityServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\Availability\AvailabilityContextInterface;
use Ibexa\Contracts\ProductCatalog\Values\Availability\AvailabilityInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Twig\Extension\RuntimeExtensionInterface;

final class ProductAvailabilityRuntime implements RuntimeExtensionInterface
{
    private ProductAvailabilityServiceInterface $availabilityService;

    public function __construct(ProductAvailabilityServiceInterface $availabilityService)
    {
        $this->availabilityService = $availabilityService;
    }

    public function hasProductAvailability(ProductInterface $product): bool
    {
        return $this->availabilityService->hasAvailability($product);
    }

    public function getProductAvailability(
        ProductInterface $product,
        ?AvailabilityContextInterface $context = null
    ): ?AvailabilityInterface {
        if ($this->availabilityService->hasAvailability($product)) {
            return $this->availabilityService->getAvailability($product, $context);
        }

        return null;
    }

    public function isProductAvailable(
        ProductInterface $product,
        ?AvailabilityContextInterface $context = null
    ): bool {
        if ($this->availabilityService->hasAvailability($product)) {
            return $this->availabilityService->getAvailability($product, $context)->isAvailable();
        }

        return false;
    }

    public function getProductStock(
        ProductInterface $product,
        ?AvailabilityContextInterface $context = null
    ): ?int {
        if ($this->availabilityService->hasAvailability($product)) {
            return $this->availabilityService->getAvailability($product, $context)->getStock();
        }

        return null;
    }
}
