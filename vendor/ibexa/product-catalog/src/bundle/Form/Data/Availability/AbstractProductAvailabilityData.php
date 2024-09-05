<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Data\Availability;

use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Symfony\Component\Validator\Constraints as Assert;

abstract class AbstractProductAvailabilityData
{
    protected ProductInterface $product;

    protected ?bool $available;

    protected ?bool $infinite;

    /**
     * @Assert\PositiveOrZero()
     */
    protected ?int $stock;

    public function __construct(ProductInterface $product)
    {
        $this->product = $product;
        $this->available = null;
        $this->infinite = null;
        $this->stock = null;
    }

    public function getProduct(): ProductInterface
    {
        return $this->product;
    }

    public function isAvailable(): ?bool
    {
        return $this->available;
    }

    /**
     * @return $this
     */
    public function setAvailable(?bool $available): self
    {
        $this->available = $available;

        return $this;
    }

    public function getStock(): ?int
    {
        return $this->stock;
    }

    public function setStock(?int $stock): self
    {
        $this->stock = $stock;

        return $this;
    }

    public function isInfinite(): ?bool
    {
        return $this->infinite;
    }

    public function setInfinite(?bool $infinite): void
    {
        $this->infinite = $infinite;
    }
}
