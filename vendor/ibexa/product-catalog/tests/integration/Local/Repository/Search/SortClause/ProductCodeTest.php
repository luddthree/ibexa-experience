<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Local\Repository\Search\SortClause;

use Ibexa\Contracts\ProductCatalog\Values\Product\ProductQuery;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\SortClause\ProductCode;

final class ProductCodeTest extends AbstractSortClauseTestCase
{
    public function dataProviderForTestSortClause(): iterable
    {
        yield ProductQuery::SORT_ASC => [
            new ProductCode(ProductQuery::SORT_ASC),
            [
                '0001',
                '0002',
                '0003',
                'ATTRIBUTE_SEARCH_CHECK_0001',
                'ATTRIBUTE_SEARCH_CHECK_0002',
                'ATTRIBUTE_SEARCH_CHECK_0003',
                'ATTRIBUTE_SEARCH_CHECK_0004',
                'JEANS_A',
                'JEANS_B',
                'SHIRTA',
                'SHIRTB',
                'TROUSERS_0001',
                'WARRANTY_0001',
            ],
        ];

        yield ProductQuery::SORT_DESC => [
            new ProductCode(ProductQuery::SORT_DESC),
            [
                'WARRANTY_0001',
                'TROUSERS_0001',
                'SHIRTB',
                'SHIRTA',
                'JEANS_B',
                'JEANS_A',
                'ATTRIBUTE_SEARCH_CHECK_0004',
                'ATTRIBUTE_SEARCH_CHECK_0003',
                'ATTRIBUTE_SEARCH_CHECK_0002',
                'ATTRIBUTE_SEARCH_CHECK_0001',
                '0003',
                '0002',
                '0001',
            ],
        ];
    }
}
