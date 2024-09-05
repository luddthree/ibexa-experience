<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Values\Currency;

use Countable;
use IteratorAggregate;

/**
 * @extends IteratorAggregate<\Ibexa\Contracts\ProductCatalog\Values\CurrencyInterface>
 */
interface CurrencyListInterface extends IteratorAggregate, Countable
{
    /**
     * Partial list of currencies.
     *
     * @return \Ibexa\Contracts\ProductCatalog\Values\CurrencyInterface[]
     */
    public function getCurrencies(): array;

    /**
     * Return total count of found currencies (filtered by permissions).
     */
    public function getTotalCount(): int;
}
