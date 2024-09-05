<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\Query\AggregationVisitor;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation\AbstractStatsAggregation;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation\RawStatsAggregation;
use Ibexa\Contracts\Elasticsearch\Query\LanguageFilter;

final class RawStatsAggregationVisitor extends AbstractStatsAggregationVisitor
{
    public function supports(Aggregation $aggregation, LanguageFilter $languageFilter): bool
    {
        return $aggregation instanceof RawStatsAggregation;
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation\RawStatsAggregation $aggregation
     */
    public function getTargetField(AbstractStatsAggregation $aggregation): string
    {
        return $aggregation->getFieldName();
    }
}

class_alias(RawStatsAggregationVisitor::class, 'Ibexa\Platform\ElasticSearchEngine\Query\AggregationVisitor\RawStatsAggregationVisitor');
