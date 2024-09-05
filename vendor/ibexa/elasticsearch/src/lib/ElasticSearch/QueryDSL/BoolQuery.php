<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\ElasticSearch\QueryDSL;

final class BoolQuery implements Query
{
    /** @var \Ibexa\Elasticsearch\ElasticSearch\QueryDSL\Query[] */
    private $must;

    /** @var \Ibexa\Elasticsearch\ElasticSearch\QueryDSL\Query[] */
    private $filter;

    /** @var \Ibexa\Elasticsearch\ElasticSearch\QueryDSL\Query[] */
    private $should;

    /** @var \Ibexa\Elasticsearch\ElasticSearch\QueryDSL\Query[] */
    private $mustNot;

    /** @var int */
    private $minimumShouldMatch;

    public function __construct(
        array $must = [],
        array $filter = [],
        array $should = [],
        array $mustNot = [],
        int $minimumShouldMatch = 1
    ) {
        $this->must = $must;
        $this->filter = $filter;
        $this->should = $should;
        $this->mustNot = $mustNot;
        $this->minimumShouldMatch = $minimumShouldMatch;
    }

    public function addMust(Query $node): self
    {
        $this->must[] = $node;

        return $this;
    }

    public function addFilter(Query $node): self
    {
        $this->filter[] = $node;

        return $this;
    }

    public function addShould(Query $node): self
    {
        $this->should[] = $node;

        return $this;
    }

    public function addMustNot(Query $node): self
    {
        $this->mustNot[] = $node;

        return $this;
    }

    public function setMinimumShouldMatch(int $minimumShouldMatch): self
    {
        $this->minimumShouldMatch = $minimumShouldMatch;

        return $this;
    }

    public function toArray(): array
    {
        $query = [];

        if (!empty($this->must)) {
            $query['must'] = $this->buildNodes($this->must);
        }

        if (!empty($this->filter)) {
            $query['filter'] = $this->buildNodes($this->filter);
        }

        if (!empty($this->should)) {
            $query['should'] = $this->buildNodes($this->should);
            $query['minimum_should_match'] = $this->minimumShouldMatch;
        }

        if (!empty($this->mustNot)) {
            $query['must_not'] = $this->buildNodes($this->mustNot);
        }

        return [
            'bool' => $query,
        ];
    }

    private function buildNodes(array $nodes): array
    {
        return array_map(static function (Query $node): array {
            return $node->toArray();
        }, $nodes);
    }
}

class_alias(BoolQuery::class, 'Ibexa\Platform\ElasticSearchEngine\ElasticSearch\QueryDSL\BoolQuery');
