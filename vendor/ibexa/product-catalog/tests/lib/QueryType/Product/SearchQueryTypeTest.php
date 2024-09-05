<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\QueryType\Product;

use Ibexa\Contracts\ProductCatalog\Values\Product\ProductQuery;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\ProductCategory;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\SortClause\ProductName;
use Ibexa\ProductCatalog\QueryType\Product\SearchQueryType;
use PHPUnit\Framework\TestCase;

/**
 * @phpstan-import-type TQueryParams from \Ibexa\ProductCatalog\QueryType\Product\SearchQueryType
 */
final class SearchQueryTypeTest extends TestCase
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
            (new SearchQueryType())->getQuery($parameters)
        );
    }

    /**
     * @return iterable<string, array{
     *     array{
     *          query_string?: string|null,
     *          criteria?: array<\Ibexa\Contracts\ProductCatalog\Values\Product\Query\CriterionInterface>,
     *          filter?: \Ibexa\Contracts\ProductCatalog\Values\Product\Query\CriterionInterface,
     *          sort_clauses?: array<\Ibexa\Contracts\ProductCatalog\Values\Product\Query\SortClause>,
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

        yield 'null' => [
            ['query_string' => null],
            $productQuery,
        ];

        yield 'query_string' => [
            ['query_string' => 'foo'],
            new ProductQuery(
                null,
                new Criterion\LogicalOr([
                    new Criterion\ProductCode(['foo']),
                    new Criterion\ProductName('*foo*'),
                ])
            ),
        ];

        yield 'query_string and criteria' => [
            [
                'query_string' => 'foo',
                'criteria' => [new ProductCategory([1])],
            ],
            new ProductQuery(
                null,
                new Criterion\LogicalAnd([
                    new ProductCategory([1]),
                    new Criterion\LogicalOr([
                        new Criterion\ProductCode(['foo']),
                        new Criterion\ProductName('*foo*'),
                    ]),
                ])
            ),
        ];

        yield 'criteria' => [
            [
                'criteria' => [new ProductCategory([1])],
            ],
            new ProductQuery(
                null,
                new Criterion\LogicalAnd([
                    new ProductCategory([1]),
                ])
            ),
        ];

        yield 'filter and sort clauses' => [
            [
                'filter' => new Criterion\MatchAll(),
                'sort_clauses' => [new ProductName(ProductQuery::SORT_ASC)],
            ],
            new ProductQuery(
                new Criterion\MatchAll(),
                null,
                [new ProductName(ProductQuery::SORT_ASC)]
            ),
        ];
    }

    public function testGetSupportedParameters(): void
    {
        self::assertSame(
            [
                'query_string',
                'criteria',
                'filter',
                'sort_clauses',
                'category_id',
                'exclude_category_id',
            ],
            (new SearchQueryType())->getSupportedParameters()
        );
    }

    public function testGetName(): void
    {
        self::assertSame(
            'IbexaProductSearch',
            SearchQueryType::getName()
        );
    }
}
