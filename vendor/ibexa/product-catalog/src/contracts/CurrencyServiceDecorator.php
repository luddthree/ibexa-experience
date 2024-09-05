<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog;

use Ibexa\Contracts\ProductCatalog\Values\Currency\CurrencyCreateStruct;
use Ibexa\Contracts\ProductCatalog\Values\Currency\CurrencyListInterface;
use Ibexa\Contracts\ProductCatalog\Values\Currency\CurrencyQuery;
use Ibexa\Contracts\ProductCatalog\Values\Currency\CurrencyUpdateStruct;
use Ibexa\Contracts\ProductCatalog\Values\CurrencyInterface;

abstract class CurrencyServiceDecorator implements CurrencyServiceInterface
{
    protected CurrencyServiceInterface $innerService;

    public function __construct(CurrencyServiceInterface $innerService)
    {
        $this->innerService = $innerService;
    }

    public function createCurrency(CurrencyCreateStruct $struct): CurrencyInterface
    {
        return $this->innerService->createCurrency($struct);
    }

    public function deleteCurrency(CurrencyInterface $currency): void
    {
        $this->innerService->deleteCurrency($currency);
    }

    public function getCurrency(int $id): CurrencyInterface
    {
        return $this->innerService->getCurrency($id);
    }

    public function getCurrencyByCode(string $code): CurrencyInterface
    {
        return $this->innerService->getCurrencyByCode($code);
    }

    public function findCurrencies(?CurrencyQuery $query = null): CurrencyListInterface
    {
        return $this->innerService->findCurrencies($query);
    }

    public function updateCurrency(CurrencyInterface $currency, CurrencyUpdateStruct $struct): CurrencyInterface
    {
        return $this->innerService->updateCurrency($currency, $struct);
    }
}
