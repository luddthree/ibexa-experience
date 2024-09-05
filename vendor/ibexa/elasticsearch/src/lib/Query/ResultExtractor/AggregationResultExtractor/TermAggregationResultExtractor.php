<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\Query\ResultExtractor\AggregationResultExtractor;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\AggregationResult;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\AggregationResult\TermAggregationResult;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\AggregationResult\TermAggregationResultEntry;
use Ibexa\Contracts\Elasticsearch\Query\AggregationResultExtractor;
use Ibexa\Contracts\Elasticsearch\Query\LanguageFilter;
use Ibexa\Elasticsearch\Query\ResultExtractor\AggregationResultExtractor\TermAggregationKeyMapper\NullAggregationKeyMapper;

final class TermAggregationResultExtractor implements AggregationResultExtractor
{
    /** @var string */
    private $aggregationClass;

    /** @var \Ibexa\Elasticsearch\Query\ResultExtractor\AggregationResultExtractor\TermAggregationKeyMapper */
    private $keyMapper;

    public function __construct(string $aggregationClass, ?TermAggregationKeyMapper $keyMapper = null)
    {
        if ($keyMapper === null) {
            $keyMapper = new NullAggregationKeyMapper();
        }

        $this->aggregationClass = $aggregationClass;
        $this->keyMapper = $keyMapper;
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
        $entries = [];

        $mappedKeys = $this->keyMapper->map(
            $aggregation,
            $languageFilter,
            array_column($data['buckets'], 'key')
        );

        foreach ($data['buckets'] as $bucket) {
            $key = $bucket['key'];

            if (isset($mappedKeys[$key])) {
                $entries[] = new TermAggregationResultEntry(
                    $mappedKeys[$key],
                    $bucket['doc_count']
                );
            }
        }

        return new TermAggregationResult($aggregation->getName(), $entries);
    }
}

class_alias(TermAggregationResultExtractor::class, 'Ibexa\Platform\ElasticSearchEngine\Query\ResultExtractor\AggregationResultExtractor\TermAggregationResultExtractor');
