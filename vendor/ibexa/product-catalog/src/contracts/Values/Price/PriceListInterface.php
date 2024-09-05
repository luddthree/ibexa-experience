<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Values\Price;

use Countable;
use IteratorAggregate;

/**
 * @extends IteratorAggregate<\Ibexa\Contracts\ProductCatalog\Values\PriceInterface>
 */
interface PriceListInterface extends IteratorAggregate, Countable
{
    /**
     * Partial list of prices.
     *
     * @return \Ibexa\Contracts\ProductCatalog\Values\PriceInterface[]
     */
    public function getPrices(): array;

    /**
     * Return total count of found prices.
     */
    public function getTotalCount(): int;
}
