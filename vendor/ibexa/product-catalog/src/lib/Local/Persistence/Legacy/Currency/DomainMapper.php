<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Legacy\Currency;

use Ibexa\ProductCatalog\Local\Persistence\Values\Currency as SpiCurrency;
use Ibexa\ProductCatalog\Local\Repository\Values\Currency;

final class DomainMapper implements DomainMapperInterface
{
    public function createFromSpi(SpiCurrency $spiCurrency): Currency
    {
        return new Currency($spiCurrency->id, $spiCurrency->code, $spiCurrency->subunits, $spiCurrency->enabled);
    }
}
