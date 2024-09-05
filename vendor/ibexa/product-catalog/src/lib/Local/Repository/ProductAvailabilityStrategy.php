<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository;

use Ibexa\Contracts\ProductCatalog\ProductAvailabilityStrategyInterface;
use Ibexa\Contracts\ProductCatalog\Values\Availability\AvailabilityContextInterface;
use Ibexa\Contracts\ProductCatalog\Values\Availability\AvailabilityInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\ProductAvailability\HandlerInterface;
use Ibexa\ProductCatalog\Local\Repository\Values\Availability;
use Ibexa\ProductCatalog\Local\Repository\Values\AvailabilityContext\AvailabilityContext;

final class ProductAvailabilityStrategy implements ProductAvailabilityStrategyInterface
{
    private HandlerInterface $handler;

    public function __construct(HandlerInterface $handler)
    {
        $this->handler = $handler;
    }

    public function accept(AvailabilityContextInterface $context): bool
    {
        return $context instanceof AvailabilityContext;
    }

    /**
     * @param \Ibexa\ProductCatalog\Local\Repository\Values\AvailabilityContext\AvailabilityContext $context
     */
    public function getProductAvailability(
        ProductInterface $product,
        AvailabilityContextInterface $context
    ): AvailabilityInterface {
        $productAvailability = $this->handler->find($product->getCode());
        $isAvailable = $productAvailability->isAvailable();
        $requestedAmount = $context->getRequestedAmount();
        $stock = $productAvailability->getStock();

        return new Availability(
            $product,
            $this->calculateAvailability($isAvailable, $requestedAmount, $stock),
            $productAvailability->isInfinite(),
            $stock
        );
    }

    private function calculateAvailability(
        bool $isAvailable,
        int $requestedAmount,
        ?int $stock
    ): bool {
        if ($isAvailable === false) {
            return false;
        }

        if ($stock === null) {
            return true;
        }

        if ($requestedAmount <= $stock) {
            return true;
        }

        return false;
    }
}
