<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog;

use Ibexa\Contracts\ProductCatalog\Values\Availability\AvailabilityContextInterface;
use Ibexa\Contracts\ProductCatalog\Values\Availability\AvailabilityInterface;
use Ibexa\Contracts\ProductCatalog\Values\Availability\ProductAvailabilityCreateStruct;
use Ibexa\Contracts\ProductCatalog\Values\Availability\ProductAvailabilityUpdateStruct;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;

abstract class ProductAvailabilityServiceDecorator implements ProductAvailabilityServiceInterface
{
    protected ProductAvailabilityServiceInterface $innerService;

    public function __construct(ProductAvailabilityServiceInterface $innerService)
    {
        $this->innerService = $innerService;
    }

    public function getAvailability(
        ProductInterface $product,
        ?AvailabilityContextInterface $availabilityContext = null
    ): AvailabilityInterface {
        return $this->innerService->getAvailability($product, $availabilityContext);
    }

    public function hasAvailability(ProductInterface $product): bool
    {
        return $this->innerService->hasAvailability($product);
    }

    public function createProductAvailability(ProductAvailabilityCreateStruct $struct): AvailabilityInterface
    {
        return $this->innerService->createProductAvailability($struct);
    }

    public function updateProductAvailability(ProductAvailabilityUpdateStruct $struct): AvailabilityInterface
    {
        return $this->innerService->updateProductAvailability($struct);
    }

    public function increaseProductAvailability(ProductInterface $product, int $amount = 1): AvailabilityInterface
    {
        return $this->innerService->increaseProductAvailability($product, $amount);
    }

    public function decreaseProductAvailability(ProductInterface $product, int $amount = 1): AvailabilityInterface
    {
        return $this->innerService->decreaseProductAvailability($product, $amount);
    }

    public function deleteProductAvailability(
        ProductInterface $product
    ): void {
        $this->innerService->deleteProductAvailability($product);
    }
}
