<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Input\Parser\Aggregation;

use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Aggregation\ProductAvailabilityTermAggregation;
use Ibexa\Contracts\Rest\Exceptions;
use Ibexa\Contracts\Rest\Input\ParsingDispatcher;
use Ibexa\Rest\Server\Input\Parser\Aggregation\AbstractTermAggregationParser;

final class ProductAvailabilityTermAggregationParser extends AbstractTermAggregationParser
{
    protected function getAggregationName(): string
    {
        return 'ProductAvailabilityTerm';
    }

    /**
     * @param array{name: string} $data
     */
    protected function parseAggregation(
        array $data,
        ParsingDispatcher $parsingDispatcher
    ): ProductAvailabilityTermAggregation {
        if (!array_key_exists('name', $data)) {
            throw new Exceptions\Parser("Missing 'name' element for {$this->getAggregationName()}.");
        }

        return new ProductAvailabilityTermAggregation($data['name']);
    }
}
