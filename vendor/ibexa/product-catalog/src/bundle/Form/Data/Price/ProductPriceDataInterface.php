<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Data\Price;

use Ibexa\Contracts\ProductCatalog\Values\CurrencyInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;

interface ProductPriceDataInterface
{
    /**
     * @return numeric-string|null
     */
    public function getBasePrice(): ?string;

    /**
     * @param numeric-string|null $basePrice
     *
     * @return $this
     */
    public function setBasePrice(?string $basePrice): self;

    /**
     * @return numeric-string|null
     */
    public function getCustomPrice(): ?string;

    /**
     * @param numeric-string|null $customPrice
     *
     * @return $this
     */
    public function setCustomPrice(?string $customPrice): self;

    public function getCurrency(): CurrencyInterface;

    public function getProduct(): ProductInterface;

    /**
     * @param numeric-string|null $customPriceRule
     */
    public function setCustomPriceRule(?string $customPriceRule): self;

    /**
     * @return numeric-string|null
     */
    public function getCustomPriceRule(): ?string;
}
