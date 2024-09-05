<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Iterator\BatchIteratorAdapter;

use Ibexa\Contracts\Core\Repository\Iterator\BatchIteratorAdapter;
use Ibexa\Contracts\ProductCatalog\CurrencyServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\Currency\CurrencyQuery;
use Iterator;

final class CurrencyFetchAdapter implements BatchIteratorAdapter
{
    private CurrencyServiceInterface $currencyService;

    private CurrencyQuery $query;

    public function __construct(CurrencyServiceInterface $currencyService, ?CurrencyQuery $query = null)
    {
        $this->currencyService = $currencyService;
        $this->query = $query ?? new CurrencyQuery();
    }

    /**
     * @return \Iterator<\Ibexa\Contracts\ProductCatalog\Values\CurrencyInterface>
     */
    public function fetch(int $offset, int $limit): Iterator
    {
        $query = clone $this->query;
        $query->setOffset($offset);
        $query->setLimit($limit);

        /** @var \ArrayIterator<int,\Ibexa\Contracts\ProductCatalog\Values\CurrencyInterface> */
        return $this->currencyService->findCurrencies($query)->getIterator();
    }
}
