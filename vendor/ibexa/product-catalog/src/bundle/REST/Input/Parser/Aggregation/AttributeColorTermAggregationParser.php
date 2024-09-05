<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Input\Parser\Aggregation;

use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Aggregation\AttributeColorTermAggregation;

final class AttributeColorTermAggregationParser extends AbstractAttributeTermAggregationParser
{
    protected function getAggregationName(): string
    {
        return 'AttributeColorTerm';
    }

    protected function buildAttributeAggregation(array $data): AttributeColorTermAggregation
    {
        return new AttributeColorTermAggregation($data['name'], $data['attributeDefinitionIdentifier']);
    }
}
