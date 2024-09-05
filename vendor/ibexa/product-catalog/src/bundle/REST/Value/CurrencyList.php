<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Value;

use Ibexa\Rest\Value;

final class CurrencyList extends Value
{
    /**
     * @var \Ibexa\Bundle\ProductCatalog\REST\Value\Currency[]
     */
    public array $currencies = [];

    /**
     * @param \Ibexa\Bundle\ProductCatalog\REST\Value\Currency[] $currencies
     */
    public function __construct(array $currencies)
    {
        $this->currencies = $currencies;
    }
}
