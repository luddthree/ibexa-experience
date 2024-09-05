<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Values\Product\Query\Aggregation;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation\AbstractStatsAggregation;
use Ibexa\Contracts\ProductCatalog\Values\CurrencyInterface;

abstract class AbstractPriceStatsAggregation extends AbstractStatsAggregation
{
    protected CurrencyInterface $currency;

    public function __construct(string $name, CurrencyInterface $currency)
    {
        parent::__construct($name);

        $this->currency = $currency;
    }

    public function getCurrency(): CurrencyInterface
    {
        return $this->currency;
    }
}
