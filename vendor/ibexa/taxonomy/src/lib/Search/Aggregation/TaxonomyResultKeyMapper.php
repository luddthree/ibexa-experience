<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\Search\Aggregation;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation;
use Ibexa\Contracts\Solr\ResultExtractor\AggregationResultExtractor\TermAggregationKeyMapper as SolrTermAggregationKeyMapper;
use Ibexa\Contracts\Taxonomy\Service\TaxonomyServiceInterface;
use Ibexa\Elasticsearch\Query\ResultExtractor\AggregationResultExtractor\TermAggregationKeyMapper as ElasticsearchTermAggregationKeyMapper;
use Ibexa\Taxonomy\Exception\TaxonomyEntryNotFoundException;

final class TaxonomyResultKeyMapper implements SolrTermAggregationKeyMapper, ElasticsearchTermAggregationKeyMapper
{
    private TaxonomyServiceInterface $taxonomyService;

    public function __construct(TaxonomyServiceInterface $taxonomyService)
    {
        $this->taxonomyService = $taxonomyService;
    }

    /**
     * @param array<mixed>|\Ibexa\Contracts\Elasticsearch\Query\LanguageFilter $languageFilter
     * @param array<string> $keys
     *
     * @return array<string, \Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry>
     */
    public function map(Aggregation $aggregation, $languageFilter, array $keys): array
    {
        $result = [];
        foreach ($keys as $key) {
            try {
                $result[$key] = $this->taxonomyService->loadEntryById((int)$key);
            } catch (TaxonomyEntryNotFoundException $e) {
            }
        }

        return $result;
    }
}
