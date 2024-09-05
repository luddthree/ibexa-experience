<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Data\Price;

final class ProductPriceDeleteData
{
    /** @var \Ibexa\Contracts\ProductCatalog\Values\PriceInterface[] */
    private array $prices;

    /**
     * @param \Ibexa\Contracts\ProductCatalog\Values\PriceInterface[] $prices
     */
    public function __construct(array $prices = [])
    {
        $this->prices = $prices;
    }

    /**
     * @return \Ibexa\Contracts\ProductCatalog\Values\PriceInterface[]
     */
    public function getPrices(): array
    {
        return $this->prices;
    }

    /**
     * @param \Ibexa\Contracts\ProductCatalog\Values\PriceInterface[] $prices
     */
    public function setPrices(array $prices): void
    {
        $this->prices = $prices;
    }
}
