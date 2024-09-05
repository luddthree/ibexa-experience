<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\Values;

use Ibexa\Contracts\ProductCatalog\Values\Availability\AvailabilityInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;

final class Availability implements AvailabilityInterface
{
    private ProductInterface $product;

    private bool $availability;

    private ?int $stock;

    private bool $isInfinite;

    public function __construct(
        ProductInterface $product,
        bool $availability,
        bool $isInfinite,
        ?int $stock
    ) {
        $this->product = $product;
        $this->availability = $availability;
        $this->isInfinite = $isInfinite;
        $this->stock = $stock;
    }

    public function isAvailable(): bool
    {
        return $this->availability;
    }

    public function getStock(): ?int
    {
        return $this->stock;
    }

    public function getProduct(): ProductInterface
    {
        return $this->product;
    }

    public function isInfinite(): bool
    {
        return $this->isInfinite;
    }
}
