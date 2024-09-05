<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Local\Repository\Search\Criterion;

use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\IntegerAttributeRange;

final class IntegerAttributeRangeTestCase extends AbstractCriterionTestCase
{
    private const IDENTIFIER = 'foo_integer';

    public function dataProviderForTestCriterion(): iterable
    {
        yield [
            new IntegerAttributeRange(self::IDENTIFIER, 90, 99),
            ['ATTRIBUTE_SEARCH_CHECK_0001'],
        ];

        yield [
            new IntegerAttributeRange(self::IDENTIFIER, 90, null),
            ['ATTRIBUTE_SEARCH_CHECK_0001', 'ATTRIBUTE_SEARCH_CHECK_0004'],
        ];

        yield [
            new IntegerAttributeRange(self::IDENTIFIER, null, 100),
            ['ATTRIBUTE_SEARCH_CHECK_0001', 'ATTRIBUTE_SEARCH_CHECK_0003'],
        ];
    }
}
