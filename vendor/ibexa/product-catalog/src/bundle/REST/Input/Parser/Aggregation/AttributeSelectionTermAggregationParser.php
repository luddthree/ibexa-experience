<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Input\Parser\Aggregation;

use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Aggregation\AttributeSelectionTermAggregation;

final class AttributeSelectionTermAggregationParser extends AbstractAttributeTermAggregationParser
{
    protected function getAggregationName(): string
    {
        return 'AttributeSelectionTerm';
    }

    protected function buildAttributeAggregation(array $data): AttributeSelectionTermAggregation
    {
        return new AttributeSelectionTermAggregation($data['name'], $data['attributeDefinitionIdentifier']);
    }
}
