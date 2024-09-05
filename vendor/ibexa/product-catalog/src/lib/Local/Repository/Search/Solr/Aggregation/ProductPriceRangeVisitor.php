<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\Search\Solr\Aggregation;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation\AbstractRangeAggregation;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Aggregation\ProductPriceRangeAggregation;
use Ibexa\ProductCatalog\Local\Repository\Search\Common\FieldNameBuilder\PriceFieldNameBuilder;
use Ibexa\Solr\Query\Common\AggregationVisitor\AbstractRangeAggregationVisitor;

final class ProductPriceRangeVisitor extends AbstractRangeAggregationVisitor
{
    /**
     * @param \Ibexa\Contracts\ProductCatalog\Values\Product\Query\Aggregation\ProductPriceRangeAggregation $aggregation
     */
    protected function getTargetField(AbstractRangeAggregation $aggregation): string
    {
        $builder = new PriceFieldNameBuilder($aggregation->getCurrencyCode());

        return $builder->build() . '_i';
    }

    /**
     * @param array{languages: array<string>} $languageFilter
     */
    public function canVisit(Aggregation $aggregation, array $languageFilter): bool
    {
        return $aggregation instanceof ProductPriceRangeAggregation;
    }
}
