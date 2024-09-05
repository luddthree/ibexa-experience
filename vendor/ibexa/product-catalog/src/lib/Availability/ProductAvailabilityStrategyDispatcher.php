<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Availability;

use Ibexa\Contracts\ProductCatalog\Values\Availability\AvailabilityContextInterface;
use Ibexa\Contracts\ProductCatalog\Values\Availability\AvailabilityInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;

final class ProductAvailabilityStrategyDispatcher
{
    /** @var iterable<\Ibexa\Contracts\ProductCatalog\ProductAvailabilityStrategyInterface> */
    private iterable $strategies;

    /**
     * @param iterable<\Ibexa\Contracts\ProductCatalog\ProductAvailabilityStrategyInterface> $strategies
     */
    public function __construct(
        iterable $strategies
    ) {
        $this->strategies = $strategies;
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function dispatch(
        ProductInterface $product,
        AvailabilityContextInterface $availabilityContext
    ): AvailabilityInterface {
        foreach ($this->strategies as $strategy) {
            if ($strategy->accept($availabilityContext)) {
                return $strategy->getProductAvailability($product, $availabilityContext);
            }
        }

        throw new InvalidArgumentException(
            'availabilityContext',
            'Unable to find ProductAvailability that can accept ' . get_class($availabilityContext)
        );
    }
}
