<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Iterator\BatchIteratorAdapter;

use ArrayIterator;
use Ibexa\Contracts\ProductCatalog\CurrencyServiceInterface;
use Ibexa\Contracts\ProductCatalog\Iterator\BatchIteratorAdapter\CurrencyFetchAdapter;
use Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\CriterionInterface;
use Ibexa\Contracts\ProductCatalog\Values\Currency\CurrencyListInterface;
use Ibexa\Contracts\ProductCatalog\Values\Currency\CurrencyQuery;
use Ibexa\Contracts\ProductCatalog\Values\Currency\Query\FieldValueSortClause;
use Ibexa\Contracts\ProductCatalog\Values\CurrencyInterface;
use PHPUnit\Framework\TestCase;

final class CurrencyFetchAdapterTest extends TestCase
{
    private const EXAMPLE_OFFSET = 10;
    private const EXAMPLE_LIMIT = 25;

    public function testFetch(): void
    {
        $criterion = $this->createMock(CriterionInterface::class);
        $sortClauses = [
            $this->createMock(FieldValueSortClause::class),
        ];

        $expectedResults = [
            $this->createMock(CurrencyInterface::class),
            $this->createMock(CurrencyInterface::class),
            $this->createMock(CurrencyInterface::class),
        ];

        $currencyService = $this->createMock(CurrencyServiceInterface::class);
        $currencyService
            ->method('findCurrencies')
            ->with(new CurrencyQuery(
                $criterion,
                $sortClauses,
                self::EXAMPLE_LIMIT,
                self::EXAMPLE_OFFSET
            ))
            ->willReturn($this->createCurrenciesList($expectedResults));

        $originalQuery = new CurrencyQuery($criterion, $sortClauses, 0, 0);

        $adapter = new CurrencyFetchAdapter($currencyService, $originalQuery);

        self::assertSame(
            $expectedResults,
            iterator_to_array($adapter->fetch(self::EXAMPLE_OFFSET, self::EXAMPLE_LIMIT))
        );

        // Input $query remains untouched
        self::assertSame(0, $originalQuery->getOffset());
        self::assertSame(0, $originalQuery->getLimit());
    }

    /**
     * @param \Ibexa\Contracts\ProductCatalog\Values\CurrencyInterface[] $currencies
     */
    private function createCurrenciesList(array $currencies): CurrencyListInterface
    {
        $list = $this->createMock(CurrencyListInterface::class);
        $list->method('getIterator')->willReturn(new ArrayIterator($currencies));

        return $list;
    }
}
