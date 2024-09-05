<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\Query\AggregationVisitor;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation\AbstractRangeAggregation;
use Ibexa\Contracts\Elasticsearch\Query\AggregationVisitor;
use Ibexa\Contracts\Elasticsearch\Query\LanguageFilter;
use Ibexa\Elasticsearch\ElasticSearch\QueryDSL\Range;
use Ibexa\Elasticsearch\ElasticSearch\QueryDSL\RangeAggregation;

abstract class AbstractRangeAggregationVisitor implements AggregationVisitor
{
    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation\AbstractRangeAggregation $aggregation
     */
    final public function visit(
        AggregationVisitor $dispatcher,
        Aggregation $aggregation,
        LanguageFilter $languageFilter
    ): array {
        $qb = new RangeAggregation($this->getTargetField($aggregation));
        foreach ($aggregation->getRanges() as $range) {
            $qb->withRange(new Range($range->getFrom(), $range->getTo()));
        }

        return $qb->toArray();
    }

    abstract protected function getTargetField(AbstractRangeAggregation $aggregation): string;
}

class_alias(AbstractRangeAggregationVisitor::class, 'Ibexa\Platform\ElasticSearchEngine\Query\AggregationVisitor\AbstractRangeAggregationVisitor');
