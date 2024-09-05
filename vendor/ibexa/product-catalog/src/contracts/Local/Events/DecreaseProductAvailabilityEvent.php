<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Local\Events;

use Ibexa\Contracts\Core\Repository\Event\AfterEvent;
use Ibexa\Contracts\ProductCatalog\Values\Availability\AvailabilityInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;

final class DecreaseProductAvailabilityEvent extends AfterEvent
{
    private AvailabilityInterface $productAvailability;

    private ProductInterface $product;

    private int $amount;

    public function __construct(
        AvailabilityInterface $productAvailability,
        ProductInterface $product,
        int $amount
    ) {
        $this->productAvailability = $productAvailability;
        $this->product = $product;
        $this->amount = $amount;
    }

    public function getProductAvailability(): AvailabilityInterface
    {
        return $this->productAvailability;
    }

    public function getProduct(): ProductInterface
    {
        return $this->product;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }
}
