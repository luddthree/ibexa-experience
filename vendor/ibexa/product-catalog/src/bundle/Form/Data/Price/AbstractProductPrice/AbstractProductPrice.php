<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Data\Price\AbstractProductPrice;

use Ibexa\Bundle\ProductCatalog\Form\Data\Price\ProductPriceDataInterface;

abstract class AbstractProductPrice implements ProductPriceDataInterface
{
    /**
     * @var numeric-string|null
     */
    protected ?string $basePrice = null;

    /**
     * @var numeric-string|null
     */
    protected ?string $customPrice = null;

    /**
     * @var numeric-string|null
     */
    protected ?string $customPriceRule = null;

    public function getBasePrice(): ?string
    {
        return $this->basePrice;
    }

    public function setBasePrice(?string $basePrice): self
    {
        $this->basePrice = $basePrice;

        return $this;
    }

    public function getCustomPrice(): ?string
    {
        return $this->customPrice;
    }

    public function setCustomPrice(?string $customPrice): self
    {
        $this->customPrice = $customPrice;

        return $this;
    }

    /**
     * @param numeric-string|null $customPriceRule
     */
    public function setCustomPriceRule(?string $customPriceRule): self
    {
        $this->customPriceRule = $customPriceRule;

        return $this;
    }

    public function getCustomPriceRule(): ?string
    {
        return $this->customPriceRule;
    }
}
