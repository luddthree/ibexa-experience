<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\QueryType\Product;

use Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\FieldValueCriterion;
use Ibexa\Contracts\ProductCatalog\Values\Product\ProductQuery;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\ProductStock;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\SortClause\ProductStock as ProductStockSortClause;
use Ibexa\ProductCatalog\QueryType\Product\ProductsWithLowestStockQueryType;
use PHPUnit\Framework\TestCase;

/**
 * @phpstan-import-type TQueryParams from \Ibexa\ProductCatalog\QueryType\Product\ProductsWithLowestStockQueryType
 */
final class ProductsWithLowestStockQueryTypeTest extends TestCase
{
    /**
     * @dataProvider providerForGetQuery
     *
     * @param TQueryParams $parameters
     */
    public function testGetQuery(array $parameters, ProductQuery $productQuery): void
    {
        self::assertEquals(
            $productQuery,
            (new ProductsWithLowestStockQueryType())->getQuery($parameters)
        );
    }

    /**
     * @return iterable<string, array{
     *     array{
     *          limit?: int,
     *          stock?: int,
     *     },
     *     \Ibexa\Contracts\ProductCatalog\Values\Product\ProductQuery
     * }>
     */
    public function providerForGetQuery(): iterable
    {
        $productQuery = new ProductQuery(
            null,
            null
        );

        yield 'Empty parameters' => [
            [],
            $productQuery,
        ];

        yield 'limit' => [
            ['limit' => 10],
            new ProductQuery(null, null, [], 0, 10),
        ];

        yield 'stock' => [
            ['stock' => 5],
            new ProductQuery(
                null,
                new ProductStock(
                    5,
                    FieldValueCriterion::COMPARISON_LTE
                ),
                [new ProductStockSortClause()]
            ),
        ];
    }

    public function testGetSupportedParameters(): void
    {
        self::assertSame(
            [
                'limit',
                'stock',
            ],
            (new ProductsWithLowestStockQueryType())->getSupportedParameters()
        );
    }

    public function testGetName(): void
    {
        self::assertSame(
            'IbexaProductsWithLowestStock',
            ProductsWithLowestStockQueryType::getName()
        );
    }
}
