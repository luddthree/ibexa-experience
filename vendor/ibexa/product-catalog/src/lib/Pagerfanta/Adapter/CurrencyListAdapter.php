<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Pagerfanta\Adapter;

use Ibexa\Contracts\ProductCatalog\CurrencyServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\Currency\CurrencyQuery;
use Pagerfanta\Adapter\AdapterInterface;

/**
 * @implements \Pagerfanta\Adapter\AdapterInterface<\Ibexa\Contracts\ProductCatalog\Values\CurrencyInterface>
 */
final class CurrencyListAdapter implements AdapterInterface
{
    private CurrencyServiceInterface $currencyService;

    private CurrencyQuery $query;

    public function __construct(CurrencyServiceInterface $currencyService, ?CurrencyQuery $query = null)
    {
        $this->currencyService = $currencyService;
        $this->query = $query ?? new CurrencyQuery();
    }

    public function getNbResults(): int
    {
        $query = new CurrencyQuery(
            $this->query->getQuery(),
            $this->query->getSortClauses(),
            0,
        );

        return $this->currencyService->findCurrencies($query)->getTotalCount();
    }

    public function getSlice($offset, $length): iterable
    {
        $query = new CurrencyQuery(
            $this->query->getQuery(),
            $this->query->getSortClauses(),
            $length,
            $offset,
        );

        return $this->currencyService->findCurrencies($query);
    }
}
