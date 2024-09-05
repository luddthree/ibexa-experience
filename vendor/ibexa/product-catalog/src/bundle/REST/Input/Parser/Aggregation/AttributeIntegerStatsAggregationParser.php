<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Input\Parser\Aggregation;

use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Aggregation\AttributeIntegerStatsAggregation;

final class AttributeIntegerStatsAggregationParser extends AbstractAttributeStatsAggregationParser
{
    protected function getAggregationName(): string
    {
        return 'AttributeIntegerStats';
    }

    protected function buildAttributeAggregation(array $data): AttributeIntegerStatsAggregation
    {
        return new AttributeIntegerStatsAggregation($data['name'], $data['attributeDefinitionIdentifier']);
    }
}
