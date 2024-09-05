<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\ElasticSearch\QueryDSL;

use stdClass;

/**
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-bucket-global-aggregation.html
 */
final class GlobalAggregation implements Aggregation
{
    /** @var \Ibexa\Elasticsearch\ElasticSearch\QueryDSL\Aggregation[] */
    private $aggregations;

    public function __construct(array $aggregations = [])
    {
        $this->aggregations = $aggregations;
    }

    public function addAggregation(string $name, Aggregation $aggregation): self
    {
        $this->aggregations[$name] = $aggregation;

        return $this;
    }

    public function toArray(): array
    {
        $payload = [
            'global' => new stdClass(),
            'aggs' => [],
        ];

        foreach ($this->aggregations as $name => $aggregation) {
            $payload['aggs'][$name] = $aggregation->toArray();
        }

        return $payload;
    }
}

class_alias(GlobalAggregation::class, 'Ibexa\Platform\ElasticSearchEngine\ElasticSearch\QueryDSL\GlobalAggregation');
