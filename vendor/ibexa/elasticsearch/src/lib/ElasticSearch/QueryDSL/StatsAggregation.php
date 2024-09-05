<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\ElasticSearch\QueryDSL;

final class StatsAggregation implements Aggregation
{
    /** @var string */
    private $fieldName;

    public function __construct(string $fieldName)
    {
        $this->fieldName = $fieldName;
    }

    public function toArray(): array
    {
        return [
            'stats' => [
                'field' => $this->fieldName,
            ],
        ];
    }
}

class_alias(StatsAggregation::class, 'Ibexa\Platform\ElasticSearchEngine\ElasticSearch\QueryDSL\StatsAggregation');
