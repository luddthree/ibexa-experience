<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Legacy\Currency;

use Ibexa\Contracts\ProductCatalog\Values\CurrencyInterface;
use Ibexa\ProductCatalog\Local\Persistence\Values\Currency as SpiCurrency;

interface DomainMapperInterface
{
    public function createFromSpi(SpiCurrency $spiCurrency): CurrencyInterface;
}
