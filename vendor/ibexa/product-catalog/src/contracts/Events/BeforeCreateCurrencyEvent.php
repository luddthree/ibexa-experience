<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Events;

use Ibexa\Contracts\Core\Repository\Event\BeforeEvent;
use Ibexa\Contracts\ProductCatalog\Values\Currency\CurrencyCreateStruct;
use Ibexa\Contracts\ProductCatalog\Values\CurrencyInterface;
use UnexpectedValueException;

final class BeforeCreateCurrencyEvent extends BeforeEvent
{
    private CurrencyCreateStruct $createStruct;

    private ?CurrencyInterface $resultCurrency = null;

    public function __construct(CurrencyCreateStruct $createStruct)
    {
        $this->createStruct = $createStruct;
    }

    public function getCreateStruct(): CurrencyCreateStruct
    {
        return $this->createStruct;
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
