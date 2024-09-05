<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\Query\AggregationVisitor;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation\AbstractStatsAggregation;
use Ibexa\Contracts\Elasticsearch\Query\AggregationVisitor;
use Ibexa\Contracts\Elasticsearch\Query\LanguageFilter;

abstract class AbstractStatsAggregationVisitor implements AggregationVisitor
{
    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation\AbstractStatsAggregation $aggregation
     */
    final public function visit(
        AggregationVisitor $dispatcher,
        Aggregation $aggregation,
        LanguageFilter $languageFilter
    ): array {
        return [
            'stats' => [
                'field' => $this->getTargetField($aggregation),
            ],
        ];
    }

    abstract protected function getTargetField(AbstractStatsAggregation $aggregation): string;
}

class_alias(AbstractStatsAggregationVisitor::class, 'Ibexa\Platform\ElasticSearchEngine\Query\AggregationVisitor\AbstractStatsAggregationVisitor');
