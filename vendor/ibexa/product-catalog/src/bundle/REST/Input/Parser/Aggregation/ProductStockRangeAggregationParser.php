<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Input\Parser\Aggregation;

use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Aggregation\ProductStockRangeAggregation;
use Ibexa\Contracts\Rest\Exceptions;
use Ibexa\Contracts\Rest\Input\ParsingDispatcher;
use Ibexa\Rest\Server\Input\Parser\Aggregation\AbstractRangeAggregationParser;

final class ProductStockRangeAggregationParser extends AbstractRangeAggregationParser
{
    protected function getAggregationName(): string
    {
        return 'ProductStockRange';
    }

    /**
     * @param array{name: string, ranges: non-empty-array<mixed>} $data
     */
    protected function parseAggregation(
        array $data,
        ParsingDispatcher $parsingDispatcher
    ): ProductStockRangeAggregation {
        $ranges = $this->dispatchRanges(
            $parsingDispatcher,
            $data['ranges'],
            'application/vnd.ibexa.api.internal.aggregation.range.IntRange'
        );

        if (empty($ranges)) {
            throw new Exceptions\Parser(
                "\"ranges\" property requires at least one element for {$this->getAggregationName()}"
            );
        }

        return new ProductStockRangeAggregation(
            $data['name'],
            $ranges
        );
    }
}
