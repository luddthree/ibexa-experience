<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\Query\AggregationVisitor;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation\AbstractTermAggregation;
use Ibexa\Contracts\Elasticsearch\Query\AggregationVisitor;
use Ibexa\Contracts\Elasticsearch\Query\LanguageFilter;
use Ibexa\Elasticsearch\ElasticSearch\QueryDSL\TermsAggregation;

abstract class AbstractTermAggregationVisitor implements AggregationVisitor
{
    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation\AbstractTermAggregation $aggregation
     */
    final public function visit(
        AggregationVisitor $dispatcher,
        Aggregation $aggregation,
        LanguageFilter $languageFilter
    ): array {
        $qb = new TermsAggregation($this->getTargetField($aggregation));
        $qb->withSize($aggregation->getLimit());
        $qb->withMinDocCount($aggregation->getMinCount());

        return $qb->toArray();
    }

    abstract protected function getTargetField(AbstractTermAggregation $aggregation): string;
}

class_alias(AbstractTermAggregationVisitor::class, 'Ibexa\Platform\ElasticSearchEngine\Query\AggregationVisitor\AbstractTermAggregationVisitor');
