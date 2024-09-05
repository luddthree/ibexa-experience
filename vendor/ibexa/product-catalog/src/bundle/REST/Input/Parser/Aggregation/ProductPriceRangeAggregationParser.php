<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Input\Parser\Aggregation;

use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Aggregation\ProductPriceRangeAggregation;
use Ibexa\Contracts\Rest\Exceptions;
use Ibexa\Contracts\Rest\Input\ParsingDispatcher;
use Ibexa\Rest\Server\Input\Parser\Aggregation\AbstractRangeAggregationParser;

final class ProductPriceRangeAggregationParser extends AbstractRangeAggregationParser
{
    protected function getAggregationName(): string
    {
        return 'ProductPriceRange';
    }

    /**
     * @param array{name: string, currencyCode: string, ranges: non-empty-array<mixed>} $data
     */
    protected function parseAggregation(array $data, ParsingDispatcher $parsingDispatcher): ProductPriceRangeAggregation
    {
        if (!array_key_exists('currencyCode', $data)) {
            throw new Exceptions\Parser("Missing 'currencyCode' element for {$this->getAggregationName()}.");
        }

        $ranges = $this->dispatchRanges(
            $parsingDispatcher,
            $data['ranges'],
            'application/vnd.ibexa.api.internal.aggregation.range.FloatRange'
        );

        if (empty($ranges)) {
            throw new Exceptions\Parser(
                "\"ranges\" property requires at least one element for {$this->getAggregationName()}"
            );
        }

        return new ProductPriceRangeAggregation(
            $data['name'],
            $data['currencyCode'],
            $ranges
        );
    }
}
