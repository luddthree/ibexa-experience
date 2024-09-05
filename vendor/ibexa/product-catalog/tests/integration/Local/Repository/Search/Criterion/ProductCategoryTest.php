<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Local\Repository\Search\Criterion;

use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\ProductCategory;

final class ProductCategoryTest extends AbstractCriterionTestCase
{
    protected function getNotSupportedSearchEngines(): array
    {
        return ['legacy'];
    }

    public function dataProviderForTestCriterion(): iterable
    {
        yield 'exact' => [
            new ProductCategory([2]),
            ['ATTRIBUTE_SEARCH_CHECK_0003'],
        ];

        yield 'multiple' => [
            new ProductCategory([2, 3]),
            ['ATTRIBUTE_SEARCH_CHECK_0003', 'ATTRIBUTE_SEARCH_CHECK_0004'],
        ];
    }
}
