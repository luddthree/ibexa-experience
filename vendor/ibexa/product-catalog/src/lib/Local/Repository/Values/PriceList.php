<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\Values;

use ArrayIterator;
use Ibexa\Contracts\ProductCatalog\Values\Price\PriceListInterface;

final class PriceList implements PriceListInterface
{
    /** @var \Ibexa\Contracts\ProductCatalog\Values\PriceInterface[] */
    private array $prices;

    private int $totalCount;

    /**
     * @param array<\Ibexa\Contracts\ProductCatalog\Values\PriceInterface> $prices
     */
    public function __construct(array $prices, int $totalCount)
    {
        $this->prices = $prices;
        $this->totalCount = $totalCount;
    }

    /**
     * @return \ArrayIterator<int, \Ibexa\Contracts\ProductCatalog\Values\PriceInterface>
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->prices);
    }

    public function count(): int
    {
        return count($this->prices);
    }

    public function getPrices(): array
    {
        return $this->prices;
    }

    public function getTotalCount(): int
    {
        return $this->totalCount;
    }
}
