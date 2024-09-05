<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\Query\ResultExtractor\AggregationResultExtractor;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\AggregationResult;
use Ibexa\Contracts\Elasticsearch\Query\AggregationResultExtractor;
use Ibexa\Contracts\Elasticsearch\Query\LanguageFilter;

final class NestedAggregationResultExtractor implements AggregationResultExtractor
{
    /** @var \Ibexa\Contracts\Elasticsearch\Query\AggregationResultExtractor */
    private $innerResultExtractor;

    /** @var string */
    private $nestedResultKey;

    public function __construct(AggregationResultExtractor $innerResultExtractor, string $nestedResultKey)
    {
        $this->innerResultExtractor = $innerResultExtractor;
        $this->nestedResultKey = $nestedResultKey;
    }

    public function supports(Aggregation $aggregation, LanguageFilter $languageFilter): bool
    {
        return $this->innerResultExtractor->supports($aggregation, $languageFilter);
    }

    public function extract(Aggregation $aggregation, LanguageFilter $languageFilter, array $data): AggregationResult
    {
        return $this->innerResultExtractor->extract(
            $aggregation,
            $languageFilter,
            $data[$this->nestedResultKey]
        );
    }
}

class_alias(NestedAggregationResultExtractor::class, 'Ibexa\Platform\ElasticSearchEngine\Query\ResultExtractor\AggregationResultExtractor\NestedAggregationResultExtractor');
