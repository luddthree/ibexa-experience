<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\Query\ResultExtractor\AggregationResultExtractor\TermAggregationKeyMapper;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\ObjectStateService;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation;
use Ibexa\Contracts\Elasticsearch\Query\LanguageFilter;
use Ibexa\Elasticsearch\Query\ResultExtractor\AggregationResultExtractor\TermAggregationKeyMapper;

final class ObjectStateAggregationKeyMapper implements TermAggregationKeyMapper
{
    /** @var \Ibexa\Contracts\Core\Repository\ObjectStateService */
    private $objectStateService;

    public function __construct(ObjectStateService $objectStateService)
    {
        $this->objectStateService = $objectStateService;
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation\ObjectStateTermAggregation $aggregation
     */
    public function map(Aggregation $aggregation, LanguageFilter $languageFilter, array $keys): array
    {
        $result = [];
        foreach ($keys as $key) {
            try {
                $result[$key] = $this->objectStateService->loadObjectState((int)$key);
            } catch (NotFoundException $e) {
                // Skip non-existing object states
            }
        }

        return $result;
    }
}

class_alias(ObjectStateAggregationKeyMapper::class, 'Ibexa\Platform\ElasticSearchEngine\Query\ResultExtractor\AggregationResultExtractor\TermAggregationKeyMapper\ObjectStateAggregationKeyMapper');
