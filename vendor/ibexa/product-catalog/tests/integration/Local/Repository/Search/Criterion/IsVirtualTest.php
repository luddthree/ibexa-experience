<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Local\Repository\Search\Criterion;

use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\IsVirtual;

/**
 * @covers \Ibexa\ProductCatalog\Local\Repository\Search\Legacy\Criterion\IsVirtualVisitor
 */
final class IsVirtualTest extends AbstractCriterionTestCase
{
    public function dataProviderForTestCriterion(): iterable
    {
        yield [
            new IsVirtual(),
            ['WARRANTY_0001'],
        ];

        yield [
            new IsVirtual(false),
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
            ],
        ];
    }
}
