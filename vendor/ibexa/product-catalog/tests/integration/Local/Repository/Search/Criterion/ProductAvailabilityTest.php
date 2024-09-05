<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Local\Repository\Search\Criterion;

use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\ProductAvailability;

final class ProductAvailabilityTest extends AbstractCriterionTestCase
{
    public function dataProviderForTestCriterion(): iterable
    {
        yield [
            new ProductAvailability(true),
            ['0002'],
        ];

        yield [
            new ProductAvailability(false),
            [
                '0001',
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
    }
}
