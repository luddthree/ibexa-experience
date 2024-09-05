<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Events;

use Ibexa\Contracts\Core\Repository\Event\BeforeEvent;
use Ibexa\Contracts\ProductCatalog\Values\Currency\CurrencyUpdateStruct;
use Ibexa\Contracts\ProductCatalog\Values\CurrencyInterface;
use UnexpectedValueException;

final class BeforeUpdateCurrencyEvent extends BeforeEvent
{
    private CurrencyInterface $currency;

    private CurrencyUpdateStruct $updateStruct;

    private ?CurrencyInterface $resultCurrency = null;

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

    public function getResultCurrency(): CurrencyInterface
    {
        if ($this->resultCurrency === null) {
            $message = 'Return value is not set or not of type %s. Check hasResultCurrency() or'
                . ' set it using setResultCurrency() before you call the getter.';

            throw new UnexpectedValueException(sprintf($message, CurrencyInterface::class));
        }

        return $this->resultCurrency;
    }

    public function hasResultCurrency(): bool
    {
        return $this->resultCurrency instanceof CurrencyInterface;
    }

    public function setResultCurrency(?CurrencyInterface $resultCurrency): void
    {
        $this->resultCurrency = $resultCurrency;
    }
}
