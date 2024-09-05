<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Values\Product\Query\SortClause;

use Ibexa\Contracts\ProductCatalog\Values\CurrencyInterface;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\SortClause;

final class BasePrice extends SortClause
{
    private CurrencyInterface $currency;

    public function __construct(CurrencyInterface $currency, string $sortDirection = self::SORT_ASC)
    {
        parent::__construct($sortDirection);

        $this->currency = $currency;
    }

    public function getCurrency(): CurrencyInterface
    {
        return $this->currency;
    }
}
