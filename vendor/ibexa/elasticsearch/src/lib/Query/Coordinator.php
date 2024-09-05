<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\Query;

use Elasticsearch\Client;
use Exception;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchResult;
use Ibexa\Contracts\Elasticsearch\Query\AggregationVisitor;
use Ibexa\Contracts\Elasticsearch\Query\CriterionVisitor;
use Ibexa\Contracts\Elasticsearch\Query\Event\QueryFilterEvent;
use Ibexa\Contracts\Elasticsearch\Query\FacetBuilderVisitor;
use Ibexa\Contracts\Elasticsearch\Query\LanguageFilter;
use Ibexa\Contracts\Elasticsearch\Query\SortClauseVisitor;
use Ibexa\Elasticsearch\ElasticSearch\Index\IndexResolverInterface;
use Ibexa\Elasticsearch\Query\ResultExtractor\ResultExtractor;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @internal
 */
final class Coordinator implements CoordinatorInterface
{
    /** @var \Ibexa\Contracts\Elasticsearch\Query\CriterionVisitor */
    private $criterionVisitor;

    /** @var \Ibexa\Contracts\Elasticsearch\Query\SortClauseVisitor */
    private $sortClauseVisitor;

    /** @var \Ibexa\Contracts\Elasticsearch\Query\FacetBuilderVisitor */
    private $facetBuilderVisitor;

    /** @var \Ibexa\Contracts\Elasticsearch\Query\AggregationVisitor */
    private $aggregationVisitor;

    /** @var \Ibexa\Elasticsearch\Query\ResultExtractor\ResultExtractor */
    private $resultExtractor;

    /** @var \Ibexa\Elasticsearch\ElasticSearch\Index\IndexResolverInterface */
    private $indexResolver;

    /** @var \Symfony\Component\EventDispatcher\EventDispatcherInterface */
    private $eventDispatcher;

    /** @var string */
    private $documentClass;

    public function __construct(
        CriterionVisitor $criterionVisitor,
        SortClauseVisitor $sortClauseVisitor,
        FacetBuilderVisitor $facetBuilderVisitor,
        AggregationVisitor $aggregationVisitor,
        ResultExtractor $resultExtractor,
        IndexResolverInterface $indexResolver,
        EventDispatcherInterface $eventDispatcher,
        string $documentClass
    ) {
        $this->criterionVisitor = $criterionVisitor;
        $this->sortClauseVisitor = $sortClauseVisitor;
        $this->facetBuilderVisitor = $facetBuilderVisitor;
        $this->aggregationVisitor = $aggregationVisitor;
        $this->resultExtractor = $resultExtractor;
        $this->eventDispatcher = $eventDispatcher;
        $this->indexResolver = $indexResolver;
        $this->documentClass = $documentClass;
    }

    public function execute(Client $client, Query $query, array $languageFilter): SearchResult
    {
        $languageFilter = LanguageFilter::fromArray($languageFilter);
        $query = $this->applyQueryFilter($query, $languageFilter);
        $params = [
            'index' => $this->indexResolver->getIndexWildcard($this->documentClass),
            'body' => $this->buildPayload($query, $languageFilter),
            '_source_includes' => $this->resultExtractor->getExpectedSourceFields(),
        ];

        try {
            $data = $client->search($params);

            return $this->resultExtractor->extract(
                new QueryContext($query, $languageFilter),
                $data
            );
        } catch (Exception $e) {
            throw $e;
        }
    }

    private function buildPayload(Query $query, LanguageFilter $languageFilter): array
    {
        $payload = [
            'query' => [
                'bool' => [
                    'must' => $this->visitQuery($query, $languageFilter),
                    'filter' => $this->visitFilter($query, $languageFilter),
                ],
            ],
            'from' => $query->offset,
            'size' => $query->limit,
            'sort' => $this->visitSortClauses($query, $languageFilter),
            'track_total_hits' => true,
        ];

        if (!empty($query->facetBuilders) || !empty($query->aggregations)) {
            $payload['aggs'] = array_merge(
                $this->visitFacetBuilder($query, $languageFilter),
                $this->visitAggregations($query, $languageFilter)
            );
        }

        if ($query->spellcheck !== null) {
            $payload['suggest'] = $this->visitSpellcheck($query, $languageFilter);
        }

        return $payload;
    }

    private function applyQueryFilter(Query $query, LanguageFilter $languageFilter): Query
    {
        return $this->eventDispatcher->dispatch(
            new QueryFilterEvent(clone $query, $languageFilter)
        )->getQuery();
    }

    private function visitQuery(Query $query, LanguageFilter $languageFilter): array
    {
        return $this->criterionVisitor->visit(
            $this->criterionVisitor,
            $query->query ?: new Criterion\MatchAll(),
            $languageFilter
        );
    }

    private function visitFilter(Query $query, LanguageFilter $languageFilter): array
    {
        return $this->criterionVisitor->visit(
            $this->criterionVisitor,
            $query->filter ?: new Criterion\MatchAll(),
            $languageFilter
        );
    }

    private function visitSortClauses(Query $query, LanguageFilter $languageFilter): array
    {
        return array_map(function (SortClause $sortClause) use ($languageFilter): array {
            return $this->sortClauseVisitor->visit(
                $this->sortClauseVisitor,
                $sortClause,
                $languageFilter
            );
        }, $query->sortClauses);
    }

    private function visitFacetBuilder(Query $query, LanguageFilter $languageFilter): array
    {
        $aggregations = [];
        foreach ($query->facetBuilders as $facetBuilder) {
            $aggregations[$facetBuilder->name] = $this->facetBuilderVisitor->visit(
                $this->facetBuilderVisitor,
                $facetBuilder,
                $languageFilter
            );
        }

        return $aggregations;
    }

    private function visitAggregations(Query $query, LanguageFilter $languageFilter): array
    {
        $aggregations = [];
        foreach ($query->aggregations as $aggregation) {
            $aggregations[$aggregation->getName()] = $this->aggregationVisitor->visit(
                $this->aggregationVisitor,
                $aggregation,
                $languageFilter
            );
        }

        return $aggregations;
    }

    /**
     * Based on https://engineering.empathy.co/spellcheck-in-elasticsearch/.
     *
     * @return array<string, mixed>
     */
    private function visitSpellcheck(Query $query, LanguageFilter $languageFilter): array
    {
        $text = $query->spellcheck->getQuery();

        return [
            'text' => $text,
            'spellcheck_terms' => [
                'term' => [
                    'field' => 'spellcheck_content_text_spellcheck.raw',
                ],
            ],
            'spellcheck_phrase' => [
                'phrase' => [
                    'field' => 'spellcheck_content_text_spellcheck',
                    'size' => 1,
                    'direct_generator' => [
                        [
                            'field' => 'spellcheck_content_text_spellcheck',
                            'suggest_mode' => 'always',
                        ],
                    ],
                ],
            ],
        ];
    }
}

class_alias(Coordinator::class, 'Ibexa\Platform\ElasticSearchEngine\Query\Coordinator');
