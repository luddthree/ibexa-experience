<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Input\Parser\Aggregation;

use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Aggregation\AttributeIntegerRangeAggregation;
use Ibexa\Contracts\Rest\Input\ParsingDispatcher;

final class AttributeIntegerRangeAggregationParser extends AbstractAttributeRangeAggregationParser
{
    protected function getAggregationName(): string
    {
        return 'AttributeIntegerRange';
    }

    protected function buildAttributeAggregation(
        array $data,
        ParsingDispatcher $dispatcher
    ): AttributeIntegerRangeAggregation {
        return new AttributeIntegerRangeAggregation(
            $data['name'],
            $data['attributeDefinitionIdentifier'],
            $this->dispatchRanges(
                $dispatcher,
                $data['ranges'],
                'application/vnd.ibexa.api.internal.aggregation.range.IntRange'
            )
        );
    }
}
