<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Values\Product\Query\Aggregation;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation\AbstractRangeAggregation;

final class ProductPriceRangeAggregation extends AbstractRangeAggregation
{
    private string $currencyCode;

    /**
     * @phpstan-param non-empty-array<\Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation\Range> $ranges
     */
    public function __construct(string $name, string $currencyCode, array $ranges)
    {
        parent::__construct($name, $ranges);
        $this->currencyCode = $currencyCode;
    }

    public function getCurrencyCode(): string
    {
        return $this->currencyCode;
    }
}
