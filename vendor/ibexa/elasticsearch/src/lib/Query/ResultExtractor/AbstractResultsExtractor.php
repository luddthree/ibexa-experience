<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\Query\ResultExtractor;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Spellcheck;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\AggregationResultCollection;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchHit;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchResult;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\SpellcheckResult;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Contracts\Elasticsearch\Query\AggregationResultExtractor;
use Ibexa\Contracts\Elasticsearch\Query\FacetResultExtractor;
use Ibexa\Contracts\Elasticsearch\Query\LanguageFilter;
use Ibexa\Elasticsearch\Query\QueryContext;

abstract class AbstractResultsExtractor implements ResultExtractor
{
    public const MATCHED_TRANSLATION_FIELD = 'meta_indexed_language_code_s';

    /** @var \Ibexa\Contracts\Elasticsearch\Query\FacetResultExtractor */
    private $facetResultExtractor;

    /** @var \Ibexa\Contracts\Elasticsearch\Query\AggregationResultExtractor */
    private $aggregationResultExtractor;

    /** @var bool */
    private $skipMissingValueObject;

    public function __construct(
        FacetResultExtractor $facetResultExtractor,
        AggregationResultExtractor $aggregationResultExtractor,
        bool $skipMissingValueObject = true
    ) {
        $this->facetResultExtractor = $facetResultExtractor;
        $this->aggregationResultExtractor = $aggregationResultExtractor;
        $this->skipMissingValueObject = $skipMissingValueObject;
    }

    final public function extract(QueryContext $queryContext, array $data): SearchResult
    {
        $result = new SearchResult();
        $result->totalCount = $data['hits']['total']['value'];
        $result->time = $data['took'] ?? null;
        $result->maxScore = $data['hits']['max_score'] ?? null;

        foreach ($this->extractSearchHits($data['hits']['hits']) as $searchHit) {
            if ($searchHit === null) {
                --$result->totalCount;
                continue;
            }

            $result->searchHits[] = $searchHit;
        }

        if (isset($data['aggregations'])) {
            $result->facets = $this->extractFacets(
                $queryContext->getQuery()->facetBuilders,
                $data['aggregations']
            );

            $result->aggregations = $this->extractAggregations(
                $queryContext->getQuery()->aggregations,
                $queryContext->getLanguageFilter(),
                $data['aggregations']
            );
        }

        if (isset($data['suggest']['spellcheck_phrase']) || isset($data['suggest']['spellcheck_terms'])) {
            $result->spellcheck = $this->extractSpellcheckResults($queryContext->getQuery()->spellcheck, $data);
        }

        return $result;
    }

    abstract protected function loadValueObject(array $document): ValueObject;

    private function extractSearchHits(array $data): iterable
    {
        if (empty($data)) {
            yield from [];
        }

        foreach ($data as $hit) {
            try {
                $searchResultHit = new SearchHit();
                $searchResultHit->valueObject = $this->loadValueObject($hit['_source']);
                // TODO: Adapt \Ibexa\Tests\Integration\Core\Repository\SearchServiceTranslationLanguageFallbackTest::getIndexesToMatchData
                $searchResultHit->score = $hit['_score'];
                $searchResultHit->matchedTranslation = $hit['_source'][self::MATCHED_TRANSLATION_FIELD];

                yield $searchResultHit;
            } catch (NotFoundException $e) {
                if (!$this->skipMissingValueObject) {
                    throw $e;
                }

                yield null;
            }
        }
    }

    private function extractFacets(iterable $facetBuilders, array $data): array
    {
        $facets = [];
        foreach ($facetBuilders as $facetBuilder) {
            if (isset($data[$facetBuilder->name])) {
                $facets[] = $this->facetResultExtractor->extract($facetBuilder, $data[$facetBuilder->name]);
            }
        }

        return $facets;
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation[] $aggregations
     */
    private function extractAggregations(
        iterable $aggregations,
        LanguageFilter $languageFilter,
        array $data
    ): AggregationResultCollection {
        $aggregationsResults = [];
        foreach ($aggregations as $aggregation) {
            $name = $aggregation->getName();

            if (isset($data[$name])) {
                $aggregationsResults[$name] = $this->aggregationResultExtractor->extract(
                    $aggregation,
                    $languageFilter,
                    $data[$name]
                );
            }
        }

        return new AggregationResultCollection($aggregationsResults);
    }

    private function extractSpellcheckResults(Spellcheck $spellcheck, array $data): SpellcheckResult
    {
        $phraseSuggestionResults = $data['suggest']['spellcheck_phrase'][0] ?? null;
        if (!empty($phraseSuggestionResults['options'])) {
            return new SpellcheckResult(
                $phraseSuggestionResults['options'][0]['text']
            );
        }

        $query = $spellcheck->getQuery();

        $incorrect = false;
        foreach ($data['suggest']['spellcheck_terms'] ?? [] as $suggestionResult) {
            $options = $suggestionResult['options'];
            if (empty($options)) {
                continue;
            }

            $query = str_ireplace(
                $suggestionResult['text'],
                $options[0]['text'],
                $query
            );

            $incorrect = true;
        }

        return new SpellcheckResult($query, $incorrect);
    }
}

class_alias(AbstractResultsExtractor::class, 'Ibexa\Platform\ElasticSearchEngine\Query\ResultExtractor\AbstractResultsExtractor');
