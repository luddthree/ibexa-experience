<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\Query\ResultExtractor\AggregationResultExtractor;

use Ibexa\Contracts\Core\Repository\Exceptions\NotImplementedException;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\AggregationResult;
use Ibexa\Contracts\Elasticsearch\Query\AggregationResultExtractor;
use Ibexa\Contracts\Elasticsearch\Query\LanguageFilter;

final class DispatcherAggregationResultExtractor implements AggregationResultExtractor
{
    /** @var \Ibexa\Contracts\Elasticsearch\Query\AggregationResultExtractor[] */
    private $extractors;

    /**
     * @param \Ibexa\Contracts\Elasticsearch\Query\AggregationResultExtractor[] $extractors
     */
    public function __construct(iterable $extractors)
    {
        $this->extractors = $extractors;
    }

    public function supports(Aggregation $aggregation, LanguageFilter $languageFilter): bool
    {
        return $this->findExtractor($aggregation, $languageFilter) !== null;
    }

    public function extract(
        Aggregation $aggregation,
        LanguageFilter $languageFilter,
        array $data
    ): AggregationResult {
        $extractor = $this->findExtractor($aggregation, $languageFilter);

        if ($extractor === null) {
            throw new NotImplementedException(
                'No result extractor available for aggregation: ' . get_class($aggregation)
            );
        }

        return $extractor->extract($aggregation, $languageFilter, $data);
    }

    private function findExtractor(
        Aggregation $aggregation,
        LanguageFilter $languageFilter
    ): ?AggregationResultExtractor {
        foreach ($this->extractors as $extractor) {
            if ($extractor->supports($aggregation, $languageFilter)) {
                return $extractor;
            }
        }

        return null;
    }
}

class_alias(DispatcherAggregationResultExtractor::class, 'Ibexa\Platform\ElasticSearchEngine\Query\ResultExtractor\AggregationResultExtractor\DispatcherAggregationResultExtractor');
