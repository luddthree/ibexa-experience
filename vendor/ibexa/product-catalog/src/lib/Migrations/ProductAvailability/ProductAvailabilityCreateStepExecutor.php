<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Migrations\ProductAvailability;

use Ibexa\Contracts\Migration\StepExecutor\AbstractStepExecutor;
use Ibexa\Contracts\ProductCatalog\ProductAvailabilityServiceInterface;
use Ibexa\Contracts\ProductCatalog\ProductServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\Availability\AvailabilityInterface;
use Ibexa\Contracts\ProductCatalog\Values\Availability\ProductAvailabilityCreateStruct;
use Ibexa\Migration\ValueObject\Step\StepInterface;

final class ProductAvailabilityCreateStepExecutor extends AbstractStepExecutor
{
    private ProductAvailabilityServiceInterface $productAvailabilityService;

    private ProductServiceInterface $productService;

    public function __construct(
        ProductAvailabilityServiceInterface $productAvailabilityService,
        ProductServiceInterface $productService
    ) {
        $this->productAvailabilityService = $productAvailabilityService;
        $this->productService = $productService;
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    protected function doHandle(StepInterface $step): AvailabilityInterface
    {
        assert($step instanceof ProductAvailabilityCreateStep);

        $product = $this->productService->getProduct($step->getProductCode());
        $struct = new ProductAvailabilityCreateStruct(
            $product,
            $step->isAvailable(),
            $step->isInfinite(),
            $step->getStock(),
        );

        return $this->productAvailabilityService->createProductAvailability($struct);
    }

    public function canHandle(StepInterface $step): bool
    {
        return $step instanceof ProductAvailabilityCreateStep;
    }
}
