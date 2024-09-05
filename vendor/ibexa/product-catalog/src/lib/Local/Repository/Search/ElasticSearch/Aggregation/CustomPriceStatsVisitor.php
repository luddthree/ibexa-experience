<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\Search\ElasticSearch\Aggregation;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation\AbstractStatsAggregation;
use Ibexa\Contracts\Elasticsearch\Query\LanguageFilter;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Aggregation\CustomPriceStatsAggregation;
use Ibexa\Elasticsearch\Query\AggregationVisitor\AbstractStatsAggregationVisitor;
use Ibexa\ProductCatalog\Local\Repository\Search\Common\FieldNameBuilder\PriceFieldNameBuilder;

final class CustomPriceStatsVisitor extends AbstractStatsAggregationVisitor
{
    /**
     * @param \Ibexa\Contracts\ProductCatalog\Values\Product\Query\Aggregation\CustomPriceStatsAggregation $aggregation
     */
    protected function getTargetField(AbstractStatsAggregation $aggregation): string
    {
        $builder = new PriceFieldNameBuilder($aggregation->getCurrency()->getCode());
        if ($aggregation->getCustomerGroup() !== null) {
            $builder->withCustomerGroup($aggregation->getCustomerGroup()->getIdentifier());
        }

        return $builder->build() . '_i';
    }

    public function supports(Aggregation $aggregation, LanguageFilter $languageFilter): bool
    {
        return $aggregation instanceof CustomPriceStatsAggregation;
    }
}
