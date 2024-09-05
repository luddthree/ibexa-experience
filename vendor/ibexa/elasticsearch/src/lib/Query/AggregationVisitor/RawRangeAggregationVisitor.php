<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\Query\AggregationVisitor;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation\AbstractRangeAggregation;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation\RawRangeAggregation;
use Ibexa\Contracts\Elasticsearch\Query\LanguageFilter;

final class RawRangeAggregationVisitor extends AbstractRangeAggregationVisitor
{
    public function supports(Aggregation $aggregation, LanguageFilter $languageFilter): bool
    {
        return $aggregation instanceof RawRangeAggregation;
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation\RawRangeAggregation $aggregation
     */
    public function getTargetField(AbstractRangeAggregation $aggregation): string
    {
        return $aggregation->getFieldName();
    }
}

class_alias(RawRangeAggregationVisitor::class, 'Ibexa\Platform\ElasticSearchEngine\Query\AggregationVisitor\RawRangeAggregationVisitor');
