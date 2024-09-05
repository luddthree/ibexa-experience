<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Elasticsearch\Query;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\AggregationResult;

interface AggregationResultExtractor
{
    /**
     * Returns true if extractor supports given aggregation.
     */
    public function supports(Aggregation $aggregation, LanguageFilter $languageFilter): bool;

    /**
     * Transforms raw aggregation result into \Ibexa\Contracts\Core\Repository\Values\Content\Search\AggregationResult object.
     *
     * @param array $data Raw aggregation data
     */
    public function extract(Aggregation $aggregation, LanguageFilter $languageFilter, array $data): AggregationResult;
}

class_alias(AggregationResultExtractor::class, 'Ibexa\Platform\Contracts\ElasticSearchEngine\Query\AggregationResultExtractor');
