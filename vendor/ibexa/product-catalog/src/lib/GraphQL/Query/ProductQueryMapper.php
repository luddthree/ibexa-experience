<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\GraphQL\Query;

use Ibexa\Contracts\ProductCatalog\Values\Product\ProductQuery;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\SortClause;

/**
 * @internal
 */
final class ProductQueryMapper
{
    /**
     * @param array<mixed> $input
     */
    public function mapInputToQuery(array $input): ProductQuery
    {
        $criteria = $input['criteria'] ?? [];
        $query = new ProductQuery();

        if (isset($input['offset'])) {
            $query->setOffset($input['offset']);
        }
        if (isset($input['limit'])) {
            $query->setLimit($input['limit']);
        }
        if (isset($input['sortBy'])) {
            $this->setSortClauses($query, $input['sortBy']);
        }

        if (!empty($criteria)) {
            $query->setFilter(new Criterion\LogicalAnd($criteria));
        }

        return $query;
    }

    /**
     * @param string[] $clauseClasses
     */
    private function setSortClauses(ProductQuery $query, array $clauseClasses): void
    {
        $clauses = [];
        $lastSortClause = $clauseClasses[0];

        foreach ($clauseClasses as $clauseClass) {
            if ($clauseClass === ProductQuery::SORT_DESC) {
                if ($lastSortClause instanceof SortClause) {
                    $lastSortClause->setDirection($clauseClass);
                }

                continue;
            }

            if (!class_exists($clauseClass)) {
                continue;
            }

            $clauses[] = $lastSortClause = new $clauseClass();
        }

        $query->setSortClauses($clauses);
    }
}
