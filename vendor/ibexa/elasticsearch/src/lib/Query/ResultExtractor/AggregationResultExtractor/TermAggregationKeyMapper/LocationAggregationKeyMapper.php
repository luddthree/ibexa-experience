<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\Query\ResultExtractor\AggregationResultExtractor\TermAggregationKeyMapper;

use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation;
use Ibexa\Contracts\Elasticsearch\Query\LanguageFilter;
use Ibexa\Elasticsearch\Query\ResultExtractor\AggregationResultExtractor\TermAggregationKeyMapper;

final class LocationAggregationKeyMapper implements TermAggregationKeyMapper
{
    /** @var \Ibexa\Contracts\Core\Repository\LocationService */
    private $locationService;

    public function __construct(LocationService $locationService)
    {
        $this->locationService = $locationService;
    }

    public function map(Aggregation $aggregation, LanguageFilter $languageFilter, array $keys): array
    {
        $results = [];

        $locations = $this->locationService->loadLocationList($keys);
        foreach ($locations as $location) {
            $results[(string)$location->id] = $location;
        }

        return $results;
    }
}

class_alias(LocationAggregationKeyMapper::class, 'Ibexa\Platform\ElasticSearchEngine\Query\ResultExtractor\AggregationResultExtractor\TermAggregationKeyMapper\LocationAggregationKeyMapper');
