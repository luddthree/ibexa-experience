<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Events;

use Ibexa\Contracts\Core\Repository\Event\AfterEvent;
use Ibexa\Contracts\ProductCatalog\Values\Currency\CurrencyUpdateStruct;
use Ibexa\Contracts\ProductCatalog\Values\CurrencyInterface;

final class UpdateCurrencyEvent extends AfterEvent
{
    private CurrencyInterface $currency;

    private CurrencyUpdateStruct $updateStruct;

    public function __construct(CurrencyInterface $currency, CurrencyUpdateStruct $updateStruct)
    {
        $this->currency = $currency;
        $this->updateStruct = $updateStruct;
    }

    public function getCurrency(): CurrencyInterface
    {
        return $this->currency;
    }

    public function getUpdateStruct(): CurrencyUpdateStruct
    {
        return $this->updateStruct;
    }
}
