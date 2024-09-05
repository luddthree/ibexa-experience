<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Events;

use Ibexa\Contracts\Core\Repository\Event\AfterEvent;
use Ibexa\Contracts\ProductCatalog\Values\Currency\CurrencyCreateStruct;
use Ibexa\Contracts\ProductCatalog\Values\CurrencyInterface;

final class CreateCurrencyEvent extends AfterEvent
{
    private CurrencyCreateStruct $createStruct;

    private CurrencyInterface $currency;

    public function __construct(CurrencyCreateStruct $createStruct, CurrencyInterface $currency)
    {
        $this->createStruct = $createStruct;
        $this->currency = $currency;
    }

    public function getCreateStruct(): CurrencyCreateStruct
    {
        return $this->createStruct;
    }

    public function getCurrency(): CurrencyInterface
    {
        return $this->currency;
    }
}
