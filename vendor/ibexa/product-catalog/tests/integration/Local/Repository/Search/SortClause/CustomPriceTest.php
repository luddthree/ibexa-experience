<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Local\Repository\Search\SortClause;

use Ibexa\Contracts\ProductCatalog\Values\CurrencyInterface;
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroupInterface;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\BasePrice;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\Operator;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\SortClause;
use Ibexa\Core\Base\Exceptions\NotFoundException;
use Money\Money;

final class CustomPriceTest extends AbstractSortClauseTestCase
{
    protected function getAdditionalFixtures(): array
    {
        return ['price_sort_clause'];
    }

    protected function getNotSupportedSearchEngines(): array
    {
        return ['legacy'];
    }

    public function dataProviderForTestSortClause(): iterable
    {
        yield 'EUR - ASC' => [
            fn (): SortClause => new SortClause\CustomPrice($this->getCurrency('EUR')),
            [
                'A',
                'B',
                'C',
            ],
            null,
            new BasePrice(Money::EUR(0), Operator::GT),
        ];

        yield 'EUR/VIP - ASC' => [
            fn (): SortClause => new SortClause\CustomPrice(
                $this->getCurrency('EUR'),
                SortClause\CustomPrice::SORT_ASC,
                $this->getCustomGroup('vip')
            ),
            [
                'B',
                'A',
                'C',
            ],
            null,
            new BasePrice(Money::EUR(0), Operator::GT),
        ];

        yield 'EUR - DESC' => [
            fn (): SortClause => new SortClause\CustomPrice(
                $this->getCurrency('EUR'),
                SortClause\CustomPrice::SORT_DESC
            ),
            [
                'C',
                'B',
                'A',
            ],
            null,
            new BasePrice(Money::EUR(0), Operator::GT),
        ];

        yield 'EUR/VIP - DESC' => [
            fn (): SortClause => new SortClause\CustomPrice(
                $this->getCurrency('EUR'),
                SortClause\CustomPrice::SORT_DESC,
                $this->getCustomGroup('vip')
            ),
            [
                'C',
                'A',
                'B',
            ],
            null,
            new BasePrice(Money::EUR(0), Operator::GT),
        ];
    }

    private function getCurrency(string $code): CurrencyInterface
    {
        return self::getCurrencyService()->getCurrencyByCode($code);
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
