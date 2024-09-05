<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Local\Repository\Search\Criterion;

use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\BasePriceRange;
use Money\Money;

final class BasePriceRangeTestCase extends AbstractCriterionTestCase
{
    protected function getAdditionalFixtures(): array
    {
        return ['price_criterion'];
    }

    protected function getNotSupportedSearchEngines(): array
    {
        return ['legacy'];
    }

    public function dataProviderForTestCriterion(): iterable
    {
        yield [
            new BasePriceRange(Money::EUR(10000), Money::EUR(25000)),
            ['100E', '200E'],
        ];

        yield [
            new BasePriceRange(Money::EUR(10000)),
            ['100E', '200E', '300E'],
        ];

        yield [
            new BasePriceRange(null, Money::EUR(10000)),
            ['25E', '50E', '100E'],
        ];

        yield [
            new BasePriceRange(Money::USD(90000), Money::USD(110000)),
            ['100USD'],
        ];

        yield [
            new BasePriceRange(Money::USD(110000), null),
            ['200USD'],
        ];

        yield [
            new BasePriceRange(null, Money::USD(90000)),
            ['50USD'],
        ];
    }
}
