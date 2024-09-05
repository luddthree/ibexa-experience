<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Values\Product\Query\Aggregation;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation\AbstractStatsAggregation;

final class AttributeIntegerStatsAggregation extends AbstractStatsAggregation implements AttributeAggregationInterface
{
    use AttributeAggregationTrait;

    /**
     * @phpstan-param non-empty-string $attributeDefinitionIdentifier
     */
    public function __construct(string $name, string $attributeDefinitionIdentifier)
    {
        parent::__construct($name);
        $this->attributeDefinitionIdentifier = $attributeDefinitionIdentifier;
    }
}
