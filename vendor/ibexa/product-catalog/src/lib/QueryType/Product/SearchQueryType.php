<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\QueryType\Product;

use Ibexa\Contracts\ProductCatalog\QueryTypeInterface;
use Ibexa\Contracts\ProductCatalog\Values\Product\ProductQuery;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion;

/**
 * @phpstan-type TQueryParams array{
 *     query_string?: string|null,
 *     criteria?: array<\Ibexa\Contracts\ProductCatalog\Values\Product\Query\CriterionInterface>,
 *     filter?: \Ibexa\Contracts\ProductCatalog\Values\Product\Query\CriterionInterface,
 *     sort_clauses?: array<\Ibexa\Contracts\ProductCatalog\Values\Product\Query\SortClause>,
 *     exclude_category_id?: int,
 *     category_id?: int,
 * }
 */
final class SearchQueryType implements QueryTypeInterface
{
    public const NAME = 'IbexaProductSearch';

    /**
     * @phpstan-param TQueryParams $parameters
     */
    public function getQuery(array $parameters = []): ProductQuery
    {
        $query = null;
        $queryString = $parameters['query_string'] ?? null;
        $criteria = $parameters['criteria'] ?? [];
        $filter = $parameters['filter'] ?? null;
        $sortClauses = $parameters['sort_clauses'] ?? [];

        if ($queryString !== null) {
            $query = new Criterion\LogicalOr([
                new Criterion\ProductCode([$queryString]),
                new Criterion\ProductName('*' . $queryString . '*'),
            ]);
        }

        if (isset($parameters['category_id'])) {
            $criteria[] = new Criterion\ProductCategorySubtree($parameters['category_id']);
        }

        if (isset($parameters['exclude_category_id'])) {
            $criteria[] = new Criterion\LogicalNot(
                new Criterion\ProductCategorySubtree($parameters['exclude_category_id']),
            );
        }

        if (!empty($criteria)) {
            if ($query !== null) {
                $criteria[] = $query;
            }
            $query = new Criterion\LogicalAnd(
                $criteria
            );
        }

        return new ProductQuery(
            $filter,
            $query,
            $sortClauses
        );
    }

    public function getSupportedParameters(): array
    {
        return [
            'query_string',
            'criteria',
            'filter',
            'sort_clauses',
            'category_id',
            'exclude_category_id',
        ];
    }

    public static function getName(): string
    {
        return self::NAME;
    }
}
