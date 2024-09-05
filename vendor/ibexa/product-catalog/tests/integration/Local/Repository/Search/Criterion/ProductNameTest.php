<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Local\Repository\Search\Criterion;

use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\ProductName;

final class ProductNameTest extends AbstractCriterionTestCase
{
    public function dataProviderForTestCriterion(): iterable
    {
        yield 'empty' => [
            new ProductName(''),
            [],
        ];

        yield 'exact' => [
            new ProductName('Dress A'),
            ['0001'],
        ];

        if (getenv('SEARCH_ENGINE') !== 'legacy') {
            yield 'exact (case insensitivity)' => [
                new ProductName('dress a'),
                ['0001'],
            ];
        }

        yield 'wildcard' => [
            new ProductName('Jeans*'),
            ['JEANS_A', 'JEANS_B'],
        ];

        yield 'wildcards' => [
            new ProductName('*eans*'),
            ['JEANS_A', 'JEANS_B'],
        ];

        if (getenv('SEARCH_ENGINE') !== 'legacy') {
            yield 'wildcard (case insensitivity) "Dress *"' => [
                new ProductName('dress *'),
                ['0001', '0002', '0003'],
            ];

            yield 'wildcard (case insensitivity) "Jeans *"' => [
                new ProductName('Jeans *'),
                ['JEANS_A', 'JEANS_B'],
            ];
        }

        yield 'all' => [
            new ProductName('*'),
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
