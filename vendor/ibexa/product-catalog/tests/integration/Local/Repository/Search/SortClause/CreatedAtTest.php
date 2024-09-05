<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Local\Repository\Search\SortClause;

use Ibexa\Contracts\ProductCatalog\Values\Product\ProductQuery;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\SortClause\CreatedAt;

final class CreatedAtTest extends AbstractSortClauseTestCase
{
    public function dataProviderForTestSortClause(): iterable
    {
        if (getenv('SEARCH_ENGINE') === 'elasticsearch') {
            self::markTestSkipped('Skipping as sorting by publicationDate returns non-deterministic results on Elasticsearch');
        }

        yield ProductQuery::SORT_ASC => [
            new CreatedAt(ProductQuery::SORT_ASC),
            [
                '0001',
                '0002',
                '0003',
                'SHIRTA',
                'SHIRTB',
                'JEANS_A',
                'JEANS_B',
                'ATTRIBUTE_SEARCH_CHECK_0001',
                'ATTRIBUTE_SEARCH_CHECK_0002',
                'ATTRIBUTE_SEARCH_CHECK_0003',
                'ATTRIBUTE_SEARCH_CHECK_0004',
                'TROUSERS_0001',
                'WARRANTY_0001',
            ],
        ];
    }
}
