<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\ElasticSearch\QueryDSL;

final class DateRangeAggregation implements Aggregation
{
    /** @var string */
    private $fieldName;

    /** @var \Ibexa\Elasticsearch\ElasticSearch\QueryDSL\Range[] */
    private $ranges;

    /** @var string|null */
    private $format;

    public function __construct(string $fieldName, array $ranges = [], ?string $format = null)
    {
        $this->fieldName = $fieldName;
        $this->ranges = $ranges;
        $this->format = $format;
    }

    public function withRange(DateRange $range): self
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

        if ($this->format !== null) {
            $payload['format'] = $this->format;
        }

        return ['date_range' => $payload];
    }
}

class_alias(DateRangeAggregation::class, 'Ibexa\Platform\ElasticSearchEngine\ElasticSearch\QueryDSL\DateRangeAggregation');
