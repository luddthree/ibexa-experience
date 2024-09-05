<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Local\Repository\Search\SortClause;

use Ibexa\Contracts\ProductCatalog\Values\Product\ProductQuery;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\ProductCode;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\SortClause\ProductStock;

final class ProductStockTest extends AbstractSortClauseTestCase
{
    public function dataProviderForTestSortClause(): iterable
    {
        yield ProductQuery::SORT_ASC => [
            new ProductStock(ProductQuery::SORT_ASC),
            ['B', 'C', 'D', 'A'],
            null,
            new ProductCode(['A', 'B', 'C', 'D']),
        ];

        yield ProductQuery::SORT_DESC => [
            new ProductStock(ProductQuery::SORT_DESC),
            ['A', 'D', 'C', 'B'],
            null,
            new ProductCode(['A', 'B', 'C', 'D']),
        ];
    }

    protected function getAdditionalFixtures(): array
    {
        return ['availability_sort_clause'];
    }
}
