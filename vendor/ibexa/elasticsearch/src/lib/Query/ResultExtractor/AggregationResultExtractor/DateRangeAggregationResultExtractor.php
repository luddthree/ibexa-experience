<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\Query\ResultExtractor\AggregationResultExtractor;

use DateTimeImmutable;
use DateTimeInterface;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation\Range;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\AggregationResult;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\AggregationResult\RangeAggregationResult;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\AggregationResult\RangeAggregationResultEntry;
use Ibexa\Contracts\Elasticsearch\Query\AggregationResultExtractor;
use Ibexa\Contracts\Elasticsearch\Query\LanguageFilter;

final class DateRangeAggregationResultExtractor implements AggregationResultExtractor
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
        $entries = [];
        foreach ($data['buckets'] as $bucket) {
            $key = new Range(
                $this->asDateTime($bucket['from_as_string'] ?? null),
                $this->asDateTime($bucket['to_as_string'] ?? null)
            );

            $val = $bucket['doc_count'];

            $entries[] = new RangeAggregationResultEntry($key, $val);
        }

        return new RangeAggregationResult($aggregation->getName(), $entries);
    }

    private function asDateTime(?string $value): ?DateTimeInterface
    {
        if ($value === null) {
            return null;
        }

        return new DateTimeImmutable($value);
    }
}

class_alias(DateRangeAggregationResultExtractor::class, 'Ibexa\Platform\ElasticSearchEngine\Query\ResultExtractor\AggregationResultExtractor\DateRangeAggregationResultExtractor');
