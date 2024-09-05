<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Input\Parser\Aggregation;

use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Aggregation\AttributeFloatRangeAggregation;
use Ibexa\Contracts\Rest\Input\ParsingDispatcher;

final class AttributeFloatRangeAggregationParser extends AbstractAttributeRangeAggregationParser
{
    protected function getAggregationName(): string
    {
        return 'AttributeFloatRange';
    }

    protected function buildAttributeAggregation(
        array $data,
        ParsingDispatcher $dispatcher
    ): AttributeFloatRangeAggregation {
        return new AttributeFloatRangeAggregation(
            $data['name'],
            $data['attributeDefinitionIdentifier'],
            $this->dispatchRanges(
                $dispatcher,
                $data['ranges'],
                'application/vnd.ibexa.api.internal.aggregation.range.FloatRange'
            )
        );
    }
}
