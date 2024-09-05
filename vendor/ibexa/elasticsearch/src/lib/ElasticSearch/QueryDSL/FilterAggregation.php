<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\ElasticSearch\QueryDSL;

/**
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-bucket-filter-aggregation.html
 */
final class FilterAggregation implements Aggregation
{
    /** @var \Ibexa\Elasticsearch\ElasticSearch\QueryDSL\Query|null */
    private $filter;

    /** @var \Ibexa\Elasticsearch\ElasticSearch\QueryDSL\Aggregation[] */
    private $aggregations;

    public function __construct(?Query $filter = null, array $aggregations = [])
    {
        $this->filter = $filter;
        $this->aggregations = $aggregations;
    }

    public function withQuery(Query $filter): self
    {
        $this->filter = $filter;

        return $this;
    }

    public function addAggregation(string $name, Aggregation $aggregation): self
    {
        $this->aggregations[$name] = $aggregation;

        return $this;
    }

    public function toArray(): array
    {
        $payload = [
            'filter' => $this->filter->toArray(),
            'aggs' => [],
        ];

        foreach ($this->aggregations as $name => $aggregation) {
            $payload['aggs'][$name] = $aggregation->toArray();
        }

        return $payload;
    }
}

class_alias(FilterAggregation::class, 'Ibexa\Platform\ElasticSearchEngine\ElasticSearch\QueryDSL\FilterAggregation');
