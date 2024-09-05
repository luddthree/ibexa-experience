<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\ElasticSearch\QueryDSL;

final class RangeAggregation implements Aggregation
{
    /** @var string */
    private $fieldName;

    /** @var \Ibexa\Elasticsearch\ElasticSearch\QueryDSL\Range[] */
    private $ranges;

    public function __construct(string $fieldName, array $ranges = [])
    {
        $this->fieldName = $fieldName;
        $this->ranges = $ranges;
    }

    public function withRange(Range $range): self
    {
        $this->ranges[] = $range;

        return $this;
    }

    public function toArray(): array
    {
        $payload = [
            'field' => $this->fieldName,
            'ranges' => [],
        ];

        foreach ($this->ranges as $range) {
            $payload['ranges'][] = $range->toArray();
        }

        return ['range' => $payload];
    }
}

class_alias(RangeAggregation::class, 'Ibexa\Platform\ElasticSearchEngine\ElasticSearch\QueryDSL\RangeAggregation');
