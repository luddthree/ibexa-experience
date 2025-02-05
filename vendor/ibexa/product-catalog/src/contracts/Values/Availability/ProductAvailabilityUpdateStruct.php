<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Values\Availability;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;

final class ProductAvailabilityUpdateStruct extends ValueObject
{
    private ProductInterface $product;

    private ?bool $availability;

    private ?bool $isInfinite;

    private ?int $stock;

    public function __construct(
        ProductInterface $product,
        ?bool $availability = null,
        ?bool $isInfinite = null,
        ?int $stock = null
    ) {
        parent::__construct();

        $this->product = $product;
        $this->availability = $availability;
        $this->isInfinite = $isInfinite;
        $this->stock = $stock;
    }

    public function getProduct(): ProductInterface
    {
        return $this->product;
    }

    public function getAvailability(): ?bool
    {
        return $this->availability;
    }

    public function setAvailability(?bool $availability): void
    {
        $this->availability = $availability;
    }

    public function getStock(): ?int
    {
        return $this->stock ?? null;
    }

    public function setStock(?int $stock): void
    {
        $this->stock = $stock;
    }

    public function isInfinite(): ?bool
    {
        return $this->isInfinite;
    }

    public function setInfinite(?bool $infinite): void
    {
        $this->isInfinite = $infinite;
    }

    public static function createWithStock(ProductInterface $product, bool $available, ?int $stock = null): self
    {
        return new self($product, $available, false, $stock);
    }

    public static function createInfinite(ProductInterface $product, ?bool $available = null): self
    {
        return new self($product, $available, true, null);
    }
}
