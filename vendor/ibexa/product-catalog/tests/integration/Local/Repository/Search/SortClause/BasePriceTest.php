<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Local\Repository\Search\SortClause;

use Ibexa\Contracts\ProductCatalog\Values\CurrencyInterface;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\BasePrice;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\Operator;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\SortClause;
use Money\Money;

final class BasePriceTest extends AbstractSortClauseTestCase
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
            fn (): SortClause => new SortClause\BasePrice($this->getCurrency('EUR')),
            [
                'A',
                'B',
                'C',
            ],
            null,
            new BasePrice(Money::EUR(0), Operator::GT),
        ];

        yield 'EUR - DESC' => [
            fn (): SortClause => new SortClause\BasePrice(
                $this->getCurrency('EUR'),
                SortClause\BasePrice::SORT_DESC
            ),
            [
                'C',
                'B',
                'A',
            ],
            null,
            new BasePrice(Money::EUR(0), Operator::GT),
        ];
    }

    private function getCurrency(string $code): CurrencyInterface
    {
        return self::getCurrencyService()->getCurrencyByCode($code);
    }
}
