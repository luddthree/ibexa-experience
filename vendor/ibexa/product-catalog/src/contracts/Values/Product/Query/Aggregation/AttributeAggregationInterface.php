<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Values\Product\Query\Aggregation;

interface AttributeAggregationInterface
{
    /**
     * @phpstan-return non-empty-string
     */
    public function getAttributeDefinitionIdentifier(): string;
}
