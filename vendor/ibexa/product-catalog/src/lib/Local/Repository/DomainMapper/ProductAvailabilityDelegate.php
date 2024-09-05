<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\DomainMapper;

use Ibexa\Contracts\ProductCatalog\ProductAvailabilityServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\Availability\AvailabilityContextInterface;
use Ibexa\Contracts\ProductCatalog\Values\Availability\AvailabilityInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;

/**
 * @final
 */
class ProductAvailabilityDelegate
{
    private ProductAvailabilityServiceInterface $productAvailabilityService;

    public function __construct(ProductAvailabilityServiceInterface $productAvailabilityService)
    {
        $this->productAvailabilityService = $productAvailabilityService;
    }

    public function getAvailability(
        ProductInterface $product,
        ?AvailabilityContextInterface $context = null
    ): AvailabilityInterface {
        return $this->productAvailabilityService->getAvailability($product, $context);
    }

    public function hasAvailability(ProductInterface $product): bool
    {
        return $this->productAvailabilityService->hasAvailability($product);
    }
}
