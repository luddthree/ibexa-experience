<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Data\Catalog;

use Money\Currency;

final class CatalogFilterPriceData
{
    private ?Currency $currency;

    private ?float $minPrice;

    private ?float $maxPrice;

    public function __construct(
        ?Currency $currency = null,
        ?float $minPrice = null,
        ?float $maxPrice = null
    ) {
        $this->currency = $currency;
        $this->minPrice = $minPrice;
        $this->maxPrice = $maxPrice;
    }

    public function getCurrency(): ?Currency
    {
        return $this->currency;
    }

    public function getMinPrice(): ?float
    {
        return $this->minPrice;
    }

    public function getMaxPrice(): ?float
    {
        return $this->maxPrice;
    }

    public function setCurrency(?Currency $currency): void
    {
        $this->currency = $currency;
    }

    public function setMinPrice(?float $minPrice): void
    {
        $this->minPrice = $minPrice;
    }

    public function setMaxPrice(?float $maxPrice): void
    {
        $this->maxPrice = $maxPrice;
    }
}
