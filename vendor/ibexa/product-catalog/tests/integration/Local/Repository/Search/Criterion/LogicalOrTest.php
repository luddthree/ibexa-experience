<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Local\Repository\Search\Criterion;

use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\LogicalOr;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\ProductCode;

final class LogicalOrTest extends AbstractCriterionTestCase
{
    public function dataProviderForTestCriterion(): iterable
    {
        yield [
            new LogicalOr([
                new ProductCode(['0001']),
                new ProductCode(['0002']),
            ]),
            ['0001', '0002'],
        ];
    }
}
