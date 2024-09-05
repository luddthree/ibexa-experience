<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\Query\ResultExtractor\AggregationResultExtractor;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation\Range;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\AggregationResult;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\AggregationResult\RangeAggregationResult;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\AggregationResult\RangeAggregationResultEntry;
use Ibexa\Contracts\Elasticsearch\Query\AggregationResultExtractor;
use Ibexa\Contracts\Elasticsearch\Query\LanguageFilter;

final class RangeAggregationResultExtractor implements AggregationResultExtractor
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

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation\AbstractRangeAggregation $aggregation
     */
    public function extract(
        Aggregation $aggregation,
        LanguageFilter $languageFilter,
        array $data
    ): AggregationResult {
        $entries = [];
        foreach ($data['buckets'] as $bucket) {
            $from = $bucket['from'] ?? Range::INF;
            $to = $bucket['to'] ?? Range::INF;

            foreach ($aggregation->getRanges() as $range) {
                if ($range->getFrom() == $from && $range->getTo() == $to) {
                    $entries[] = new RangeAggregationResultEntry(clone $range, $bucket['doc_count']);
                    break;
                }
            }
        }

        return new RangeAggregationResult($aggregation->getName(), $entries);
    }
}

class_alias(RangeAggregationResultExtractor::class, 'Ibexa\Platform\ElasticSearchEngine\Query\ResultExtractor\AggregationResultExtractor\RangeAggregationResultExtractor');
