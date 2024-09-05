<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Data;

use Ibexa\Contracts\ProductCatalog\Values\CurrencyInterface;

final class CurrencySwitchData
{
    private ?CurrencyInterface $currency;

    public function __construct(?CurrencyInterface $currency)
    {
        $this->currency = $currency;
    }

    public function getCurrency(): ?CurrencyInterface
    {
        return $this->currency;
    }

    public function setCurrency(?CurrencyInterface $currency): void
    {
        $this->currency = $currency;
    }
}
