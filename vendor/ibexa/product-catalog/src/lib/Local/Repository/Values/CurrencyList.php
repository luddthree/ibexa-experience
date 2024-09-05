<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\Values;

use ArrayIterator;
use Ibexa\Contracts\ProductCatalog\Values\Currency\CurrencyListInterface;
use Traversable;

final class CurrencyList implements CurrencyListInterface
{
    /** @var \Ibexa\Contracts\ProductCatalog\Values\CurrencyInterface[] */
    private array $currencies;

    private int $totalCount;

    /**
     * @param \Ibexa\Contracts\ProductCatalog\Values\CurrencyInterface[] $currencies
     */
    public function __construct(array $currencies, int $total)
    {
        $this->currencies = $currencies;
        $this->totalCount = $total;
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->currencies);
    }

    public function count(): int
    {
        return count($this->currencies);
    }

    public function getCurrencies(): array
    {
        return $this->currencies;
    }

    public function getTotalCount(): int
    {
        return $this->totalCount;
    }
}
