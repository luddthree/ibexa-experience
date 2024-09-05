<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Local\Repository\Search\Criterion;

use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\FloatAttributeRange;

final class FloatAttributeRangeTestCase extends AbstractCriterionTestCase
{
    private const IDENTIFIER = 'foo_float';

    public function dataProviderForTestCriterion(): iterable
    {
        yield [
            new FloatAttributeRange(self::IDENTIFIER, 9.1, 9.9),
            ['ATTRIBUTE_SEARCH_CHECK_0001'],
        ];

        yield [
            new FloatAttributeRange(self::IDENTIFIER, 1, null),
            ['ATTRIBUTE_SEARCH_CHECK_0001', 'ATTRIBUTE_SEARCH_CHECK_0003', 'ATTRIBUTE_SEARCH_CHECK_0004'],
        ];

        yield [
            new FloatAttributeRange(self::IDENTIFIER, null, 9),
            ['ATTRIBUTE_SEARCH_CHECK_0003', 'ATTRIBUTE_SEARCH_CHECK_0004'],
        ];
    }
}
