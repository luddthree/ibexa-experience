<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\Query\AggregationVisitor\Factory;

use Ibexa\Contracts\Elasticsearch\Query\AggregationVisitor;
use Ibexa\Core\Search\Common\FieldNameResolver;
use Ibexa\Elasticsearch\Query\AggregationVisitor\AggregationFieldResolver\ContentFieldAggregationFieldResolver;
use Ibexa\Elasticsearch\Query\AggregationVisitor\DateRangeAggregationVisitor;
use Ibexa\Elasticsearch\Query\AggregationVisitor\RangeAggregationVisitor;
use Ibexa\Elasticsearch\Query\AggregationVisitor\StatsAggregationVisitor;
use Ibexa\Elasticsearch\Query\AggregationVisitor\TermAggregationVisitor;

final class ContentFieldAggregationVisitorFactory
{
    /** @var \Ibexa\Core\Search\Common\FieldNameResolver */
    private $fieldNameResolver;

    public function __construct(FieldNameResolver $fieldNameResolver)
    {
        $this->fieldNameResolver = $fieldNameResolver;
    }

    public function createDateRangeAggregationVisitor(
        string $aggregationClass,
        string $searchIndexFieldName
    ): AggregationVisitor {
        return new DateRangeAggregationVisitor(
            $aggregationClass,
            new ContentFieldAggregationFieldResolver(
                $this->fieldNameResolver,
                $searchIndexFieldName
            )
        );
    }

    public function createRangeAggregationVisitor(
        string $aggregationClass,
        string $searchIndexFieldName
    ): AggregationVisitor {
        return new RangeAggregationVisitor(
            $aggregationClass,
            new ContentFieldAggregationFieldResolver(
                $this->fieldNameResolver,
                $searchIndexFieldName
            )
        );
    }

    public function createStatsAggregationVisitor(
        string $aggregationClass,
        string $searchIndexFieldName
    ): AggregationVisitor {
        return new StatsAggregationVisitor(
            $aggregationClass,
            new ContentFieldAggregationFieldResolver(
                $this->fieldNameResolver,
                $searchIndexFieldName
            )
        );
    }

    public function createTermAggregationVisitor(
        string $aggregationClass,
        string $searchIndexFieldName
    ): AggregationVisitor {
        return new TermAggregationVisitor(
            $aggregationClass,
            new ContentFieldAggregationFieldResolver(
                $this->fieldNameResolver,
                $searchIndexFieldName
            )
        );
    }
}

class_alias(ContentFieldAggregationVisitorFactory::class, 'Ibexa\Platform\ElasticSearchEngine\Query\AggregationVisitor\Factory\ContentFieldAggregationVisitorFactory');
