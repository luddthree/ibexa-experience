<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\Query\ResultExtractor\AggregationResultExtractor;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\AggregationResult;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\AggregationResult\StatsAggregationResult;
use Ibexa\Contracts\Elasticsearch\Query\AggregationResultExtractor;
use Ibexa\Contracts\Elasticsearch\Query\LanguageFilter;

final class StatsAggregationResultExtractor implements AggregationResultExtractor
{
    /** @var string */
    private $aggregationClass;

    public function __construct(string $aggregationClass)
    {
        $this->aggregationClass = $aggregationClass;
    }

    public function supports(Aggregation $aggregation, LanguageFilter $languageFilter): bool
    {
        return $aggregation instanceof $this->aggregationClass;
    }

    public function extract(
        Aggregation $aggregation,
        LanguageFilter $languageFilter,
        array $data
    ): AggregationResult {
        return new StatsAggregationResult(
            $aggregation->getName(),
            $data['count'] ?? null,
            $data['min'] ?? null,
            $data['max'] ?? null,
            $data['avg'] ?? null,
            $data['sum'] ?? null,
        );
    }
}

class_alias(StatsAggregationResultExtractor::class, 'Ibexa\Platform\ElasticSearchEngine\Query\ResultExtractor\AggregationResultExtractor\StatsAggregationResultExtractor');
