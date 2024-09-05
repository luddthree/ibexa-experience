<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Values\Product;

use Ibexa\Contracts\ProductCatalog\Values\Product\Query\CriterionInterface;

final class ProductQuery
{
    public const SORT_ASC = 'ascending';
    public const SORT_DESC = 'descending';

    public const DEFAULT_LIMIT = 25;

    private ?CriterionInterface $filter;

    private ?CriterionInterface $query;

    /** @var \Ibexa\Contracts\ProductCatalog\Values\Product\Query\SortClause[] */
    private array $sortClauses;

    private int $offset;

    private int $limit;

    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation[] */
    private array $aggregations;

    /**
     * @param \Ibexa\Contracts\ProductCatalog\Values\Product\Query\SortClause[] $sortClauses
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation[] $aggregations
     */
    public function __construct(
        ?CriterionInterface $filter = null,
        ?CriterionInterface $query = null,
        array $sortClauses = [],
        int $offset = 0,
        int $limit = self::DEFAULT_LIMIT,
        array $aggregations = []
    ) {
        $this->filter = $filter;
        $this->query = $query;
        $this->sortClauses = $sortClauses;
        $this->offset = $offset;
        $this->limit = $limit;
        $this->aggregations = $aggregations;
    }

    public function getFilter(): ?CriterionInterface
    {
        return $this->filter;
    }

    public function hasFilter(): bool
    {
        return $this->filter !== null;
    }

    public function setFilter(?CriterionInterface $filter): void
    {
        $this->filter = $filter;
    }

    public function getQuery(): ?CriterionInterface
    {
        return $this->query;
    }

    public function hasQuery(): bool
    {
        return $this->query !== null;
    }

    public function setQuery(?CriterionInterface $query): void
    {
        $this->query = $query;
    }

    public function getOffset(): int
    {
        return $this->offset;
    }

    public function setOffset(int $offset): void
    {
        $this->offset = $offset;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function setLimit(int $limit): void
    {
        $this->limit = $limit;
    }

    /**
     * @return \Ibexa\Contracts\ProductCatalog\Values\Product\Query\SortClause[]
     */
    public function getSortClauses(): array
    {
        return $this->sortClauses;
    }

    /**
     * @param \Ibexa\Contracts\ProductCatalog\Values\Product\Query\SortClause[] $sortClauses
     */
    public function setSortClauses(array $sortClauses): void
    {
        $this->sortClauses = $sortClauses;
    }

    /**
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation[]
     */
    public function getAggregations(): array
    {
        return $this->aggregations;
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation[] $aggregations
     */
    public function setAggregations(array $aggregations): void
    {
        $this->aggregations = $aggregations;
    }
}
