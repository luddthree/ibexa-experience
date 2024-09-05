<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\Query\AggregationVisitor;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation\AbstractTermAggregation;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation\RawTermAggregation;
use Ibexa\Contracts\Elasticsearch\Query\LanguageFilter;

final class RawTermAggregationVisitor extends AbstractTermAggregationVisitor
{
    public function supports(Aggregation $aggregation, LanguageFilter $languageFilter): bool
    {
        return $aggregation instanceof RawTermAggregation;
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation\RawTermAggregation $aggregation
     */
    protected function getTargetField(AbstractTermAggregation $aggregation): string
    {
        return $aggregation->getFieldName();
    }
}

class_alias(RawTermAggregationVisitor::class, 'Ibexa\Platform\ElasticSearchEngine\Query\AggregationVisitor\RawTermAggregationVisitor');
