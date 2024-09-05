<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Local\Repository\Search\Criterion;

use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\ProductType;

final class ProductTypeTest extends AbstractCriterionTestCase
{
    public function dataProviderForTestCriterion(): iterable
    {
        yield 'empty' => [
            new ProductType([]),
            [],
        ];

        yield 'single' => [
            new ProductType(['dress']),
            ['0001', '0002', '0003'],
        ];

        yield 'multiple' => [
            new ProductType(['jeans', 'shirt']),
            ['SHIRTA', 'SHIRTB', 'JEANS_A', 'JEANS_B'],
        ];
    }
}
