<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\Query\AggregationVisitor\Factory;

use Ibexa\Contracts\Elasticsearch\Query\AggregationVisitor;
use Ibexa\Elasticsearch\Query\AggregationVisitor\AggregationFieldResolver\SearchFieldAggregationFieldResolver;
use Ibexa\Elasticsearch\Query\AggregationVisitor\DateRangeAggregationVisitor;
use Ibexa\Elasticsearch\Query\AggregationVisitor\RangeAggregationVisitor;
use Ibexa\Elasticsearch\Query\AggregationVisitor\StatsAggregationVisitor;
use Ibexa\Elasticsearch\Query\AggregationVisitor\TermAggregationVisitor;

final class SearchFieldAggregationVisitorFactory
{
    public function createDateRangeAggregationVisitor(
        string $aggregationClass,
        string $searchIndexFieldName
    ): AggregationVisitor {
        return new DateRangeAggregationVisitor(
            $aggregationClass,
            new SearchFieldAggregationFieldResolver($searchIndexFieldName)
        );
    }

    public function createRangeAggregationVisitor(
        string $aggregationClass,
        string $searchIndexFieldName
    ): AggregationVisitor {
        return new RangeAggregationVisitor(
            $aggregationClass,
            new SearchFieldAggregationFieldResolver($searchIndexFieldName)
        );
    }

    public function createStatsAggregationVisitor(
        string $aggregationClass,
        string $searchIndexFieldName
    ): AggregationVisitor {
        return new StatsAggregationVisitor(
            $aggregationClass,
            new SearchFieldAggregationFieldResolver($searchIndexFieldName)
        );
    }

    public function createTermAggregationVisitor(
        string $aggregationClass,
        string $searchIndexFieldName
    ): AggregationVisitor {
        return new TermAggregationVisitor(
            $aggregationClass,
            new SearchFieldAggregationFieldResolver($searchIndexFieldName)
        );
    }
}

class_alias(SearchFieldAggregationVisitorFactory::class, 'Ibexa\Platform\ElasticSearchEngine\Query\AggregationVisitor\Factory\SearchFieldAggregationVisitorFactory');
