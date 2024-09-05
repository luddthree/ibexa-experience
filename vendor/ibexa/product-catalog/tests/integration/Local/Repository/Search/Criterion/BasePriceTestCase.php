<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Local\Repository\Search\Criterion;

use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\BasePrice;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\Operator;
use Money\Money;

final class BasePriceTestCase extends AbstractCriterionTestCase
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
            new BasePrice(Money::EUR(10000)),
            ['100E'],
        ];

        yield [
            new BasePrice(Money::USD(100000)),
            ['100USD'],
        ];

        yield [
            new BasePrice(Money::EUR(10000), Operator::GT),
            ['200E', '300E'],
        ];

        yield [
            new BasePrice(Money::USD(100000), Operator::GT),
            ['200USD'],
        ];

        yield [
            new BasePrice(Money::EUR(10000), Operator::GTE),
            ['100E', '200E', '300E'],
        ];

        yield [
            new BasePrice(Money::USD(100000), Operator::GTE),
            ['100USD', '200USD'],
        ];

        yield [
            new BasePrice(Money::EUR(10000), Operator::LT),
            ['25E', '50E'],
        ];

        yield [
            new BasePrice(Money::USD(100000), Operator::LT),
            ['50USD'],
        ];

        yield [
            new BasePrice(Money::EUR(10000), Operator::LTE),
            ['25E', '50E', '100E'],
        ];

        yield [
            new BasePrice(Money::USD(100000), Operator::LTE),
            ['50USD', '100USD'],
        ];
    }
}
