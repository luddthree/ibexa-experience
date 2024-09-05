<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Input\Parser\Query;

use Ibexa\Contracts\ProductCatalog\Values\Product\ProductQuery as ProductQueryValueObject;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\LogicalAnd;
use Ibexa\Contracts\Rest\Input\ParsingDispatcher;

/**
 * @extends \Ibexa\Bundle\ProductCatalog\REST\Input\Parser\Query\AbstractQuery<
 *     \Ibexa\Contracts\ProductCatalog\Values\Product\Query\CriterionInterface,
 *     \Ibexa\Contracts\ProductCatalog\Values\Product\Query\SortClause,
 * >
 */
final class ProductQuery extends AbstractQuery
{
    protected function getAllowedKeys(): array
    {
        return [
            self::QUERY,
            self::FILTER,
            self::SORT_CLAUSES,
            self::AGGREGATIONS,
        ];
    }

    /**
     * @param array<mixed> $data
     */
    protected function buildQuery(
        array $data,
        ParsingDispatcher $parsingDispatcher
    ): ProductQueryValueObject {
        $query = new ProductQueryValueObject();

        if (array_key_exists(self::FILTER, $data) && is_array($data[self::FILTER])) {
            $criteria = $this->processCriteriaArray($data[self::FILTER], $parsingDispatcher);
            $query->setFilter(new LogicalAnd($criteria));
        }

        if (array_key_exists(self::QUERY, $data) && is_array($data[self::QUERY])) {
            $criteria = $this->processCriteriaArray($data[self::QUERY], $parsingDispatcher);
            $query->setQuery(new LogicalAnd($criteria));
        }

        if (array_key_exists(self::SORT_CLAUSES, $data)) {
            $query->setSortClauses(
                $this->processSortClauses($data[self::SORT_CLAUSES], $parsingDispatcher)
            );
        }

        if (array_key_exists(self::AGGREGATIONS, $data)) {
            $aggregations = [];
            foreach ($data[self::AGGREGATIONS] as $aggregation) {
                $aggregationName = array_key_first($aggregation);
                $aggregationData = $aggregation[$aggregationName];

                $aggregations[] = $parsingDispatcher->parse(
                    [
                        $aggregationName => $aggregationData,
                    ],
                    'application/vnd.ibexa.api.internal.aggregation.' . $aggregationName
                );
            }

            $query->setAggregations($aggregations);
        }

        return $query;
    }
}
