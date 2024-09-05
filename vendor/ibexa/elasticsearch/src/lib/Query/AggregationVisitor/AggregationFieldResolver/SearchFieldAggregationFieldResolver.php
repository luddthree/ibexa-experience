<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\Query\AggregationVisitor\AggregationFieldResolver;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation;
use Ibexa\Elasticsearch\Query\AggregationVisitor\AggregationFieldResolver;

final class SearchFieldAggregationFieldResolver implements AggregationFieldResolver
{
    /** @var string */
    private $searchIndexFieldName;

    public function __construct(string $searchIndexFieldName)
    {
        $this->searchIndexFieldName = $searchIndexFieldName;
    }

    public function resolveTargetField(Aggregation $aggregation): string
    {
        return $this->searchIndexFieldName;
    }
}

class_alias(SearchFieldAggregationFieldResolver::class, 'Ibexa\Platform\ElasticSearchEngine\Query\AggregationVisitor\AggregationFieldResolver\SearchFieldAggregationFieldResolver');
