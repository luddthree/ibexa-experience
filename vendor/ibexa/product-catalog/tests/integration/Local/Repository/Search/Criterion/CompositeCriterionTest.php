<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Local\Repository\Search\Criterion;

use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\CompositeCriterion;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\MatchAll;

final class CompositeCriterionTest extends AbstractCriterionTestCase
{
    public function dataProviderForTestCriterion(): iterable
    {
        $criterion = new class() extends CompositeCriterion {
            public function __construct()
            {
                parent::__construct(new MatchAll());
            }
        };

        yield [
            $criterion,
            [
                '0001',
                '0002',
                '0003',
                'ATTRIBUTE_SEARCH_CHECK_0001',
                'ATTRIBUTE_SEARCH_CHECK_0002',
                'ATTRIBUTE_SEARCH_CHECK_0003',
                'ATTRIBUTE_SEARCH_CHECK_0004',
                'SHIRTB',
                'SHIRTA',
                'JEANS_A',
                'JEANS_B',
                'TROUSERS_0001',
                'WARRANTY_0001',
            ],
        ];
    }
}
