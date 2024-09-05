<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\QueryType\Product;

use Ibexa\Contracts\ProductCatalog\QueryTypeInterface;
use Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\FieldValueCriterion;
use Ibexa\Contracts\ProductCatalog\Values\Product\ProductQuery;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\ProductStock as ProductStockCriterion;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\SortClause\ProductStock as ProductStockSortClause;

/**
 * @phpstan-type TQueryParams array{
 *     limit?: int,
 *     stock?: int,
 * }
 */
final class ProductsWithLowestStockQueryType implements QueryTypeInterface
{
    public const NAME = 'IbexaProductsWithLowestStock';

    /**
     * @phpstan-param TQueryParams $parameters
     */
    public function getQuery(array $parameters = []): ProductQuery
    {
        $query = new ProductQuery();

        if (isset($parameters['limit'])) {
            $query->setLimit((int) $parameters['limit']);
        }

        if (isset($parameters['stock'])) {
            $query->setQuery(
                new ProductStockCriterion(
                    (int) $parameters['stock'],
                    FieldValueCriterion::COMPARISON_LTE
                )
            );
            $query->setSortClauses([
                new ProductStockSortClause(),
            ]);
        }

        return $query;
    }

    public function getSupportedParameters(): array
    {
        return [
            'limit',
            'stock',
        ];
    }

    public static function getName(): string
    {
        return self::NAME;
    }
}
