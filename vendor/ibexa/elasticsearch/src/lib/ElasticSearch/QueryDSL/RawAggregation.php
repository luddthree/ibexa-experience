<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\ElasticSearch\QueryDSL;

final class RawAggregation implements Aggregation
{
    /** @var array */
    private $aggregation;

    public function __construct(array $aggregation)
    {
        $this->aggregation = $aggregation;
    }

    public function toArray(): array
    {
        return $this->aggregation;
    }
}

class_alias(RawAggregation::class, 'Ibexa\Platform\ElasticSearchEngine\ElasticSearch\QueryDSL\RawAggregation');
