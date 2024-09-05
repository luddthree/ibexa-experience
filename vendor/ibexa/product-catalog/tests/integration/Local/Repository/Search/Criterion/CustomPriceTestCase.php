<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Local\Repository\Search\Criterion;

use Ibexa\Contracts\ProductCatalog\Values\CustomerGroupInterface;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\CustomPrice;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\Operator;
use Ibexa\Core\Base\Exceptions\NotFoundException;
use Money\Money;

final class CustomPriceTestCase extends AbstractCriterionTestCase
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
            new CustomPrice(Money::EUR(10000)),
            ['100E'],
        ];

        yield [
            fn () => new CustomPrice(
                Money::EUR(10000),
                Operator::EQ,
                $this->getCustomGroup('vip')
            ),
            ['200E'],
        ];

        yield [
            fn () => new CustomPrice(
                Money::EUR(10000),
                Operator::EQ,
                $this->getCustomGroup('standard')
            ),
            ['100E'],
        ];

        yield [
            new CustomPrice(Money::USD(100000)),
            ['100USD'],
        ];

        yield [
            fn () => new CustomPrice(
                Money::USD(90000),
                Operator::EQ,
                $this->getCustomGroup('vip')
            ),
            ['100USD'],
        ];

        yield [
            new CustomPrice(Money::EUR(10000), Operator::GT),
            ['200E', '300E'],
        ];

        yield [
            new CustomPrice(Money::USD(100000), Operator::GT),
            ['200USD'],
        ];

        yield [
            fn () => new CustomPrice(
                Money::EUR(10000),
                Operator::GT,
                $this->getCustomGroup('vip')
            ),
            ['300E'],
        ];

        yield [
            fn () => new CustomPrice(
                Money::USD(100000),
                Operator::GT,
                $this->getCustomGroup('vip')
            ),
            ['200USD'],
        ];

        yield [
            new CustomPrice(Money::EUR(10000), Operator::GTE),
            ['100E', '200E', '300E'],
        ];

        yield [
            new CustomPrice(Money::USD(100000), Operator::GTE),
            ['100USD', '200USD'],
        ];

        yield [
            new CustomPrice(Money::EUR(10000), Operator::LT),
            ['25E', '50E'],
        ];

        yield [
            fn () => new CustomPrice(
                Money::EUR(10000),
                Operator::LT,
                $this->getCustomGroup('vip')
            ),
            ['25E', '50E', '100E'],
        ];

        yield [
            new CustomPrice(Money::USD(100000), Operator::LT),
            ['50USD'],
        ];

        yield [
            fn () => new CustomPrice(
                Money::USD(100000),
                Operator::LT,
                $this->getCustomGroup('vip')
            ),
            ['50USD', '100USD'],
        ];

        yield [
            new CustomPrice(Money::EUR(10000), Operator::LTE),
            ['25E', '50E', '100E'],
        ];

        yield [
            new CustomPrice(Money::USD(100000), Operator::LTE),
            ['50USD', '100USD'],
        ];
    }

    /**
     * @throws \Ibexa\Core\Base\Exceptions\NotFoundException
     */
    private function getCustomGroup(string $identifier): CustomerGroupInterface
    {
        $customerGroup = self::getCustomerGroupService()->getCustomerGroupByIdentifier($identifier);
        if ($customerGroup === null) {
            throw new NotFoundException(CustomerGroupInterface::class, $identifier);
        }

        return $customerGroup;
    }
}
