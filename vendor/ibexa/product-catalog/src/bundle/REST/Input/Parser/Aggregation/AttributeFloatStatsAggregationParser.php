<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Input\Parser\Aggregation;

use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Aggregation\AttributeFloatStatsAggregation;

final class AttributeFloatStatsAggregationParser extends AbstractAttributeStatsAggregationParser
{
    protected function getAggregationName(): string
    {
        return 'AttributeFloatStats';
    }

    protected function buildAttributeAggregation(array $data): AttributeFloatStatsAggregation
    {
        return new AttributeFloatStatsAggregation($data['name'], $data['attributeDefinitionIdentifier']);
    }
}
