<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Local\Repository;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\ProductCatalog\CurrencyServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\Common\Query\AbstractSortClause;
use Ibexa\Contracts\ProductCatalog\Values\Currency\CurrencyListInterface;
use Ibexa\Contracts\ProductCatalog\Values\Currency\CurrencyQuery;
use Ibexa\Contracts\ProductCatalog\Values\Currency\CurrencyUpdateStruct;
use Ibexa\Contracts\ProductCatalog\Values\Currency\Query\Criterion\CurrencyCodeCriterion;
use Ibexa\Contracts\ProductCatalog\Values\Currency\Query\Criterion\IsCurrencyEnabledCriterion;
use Ibexa\Contracts\ProductCatalog\Values\Currency\Query\FieldValueSortClause;
use Ibexa\Contracts\ProductCatalog\Values\Currency\Query\SortClause\CurrencyCode;
use Ibexa\Contracts\ProductCatalog\Values\CurrencyInterface;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;
use Ibexa\ProductCatalog\Local\Repository\Values\Currency;
use Ibexa\Tests\Integration\ProductCatalog\Fixtures\CurrencyFixture;

final class CurrencyServiceTest extends BaseCurrencyServiceTest
{
    private const NON_EXISTING_CURRENCY_ID = 42;
    private const NON_EXISTING_CURRENCY_CODE = 'non';
    private const TOO_LONG_CURRENCY_CODE = 'to-long';
    private const USD_CURRENCY_CODE = 'USD';
    private const PLN_CURRENCY_CODE = 'PLN';

    private CurrencyServiceInterface $currencyService;

    public function setUp(): void
    {
        parent::setUp();

        $this->currencyService = self::getCurrencyService();
    }

    /**
     * @dataProvider provideForFindCurrencies
     *
     * @phpstan-param callable(\Ibexa\Contracts\ProductCatalog\Values\Currency\CurrencyListInterface $currencies): void $expectations
     */
    public function testFindCurrencies(?CurrencyQuery $query, int $expectedCount, callable $expectations): void
    {
        $currencies = $this->currencyService->findCurrencies($query);
        self::assertSame($expectedCount, $currencies->getTotalCount());

        $expectations($currencies);
    }

    /**
     * @phpstan-return iterable<array{
     *     \Ibexa\Contracts\ProductCatalog\Values\Currency\CurrencyQuery|null,
     *     int,
     *     callable(\Ibexa\Contracts\ProductCatalog\Values\Currency\CurrencyListInterface $currencies): void,
     * }>
     */
    public function provideForFindCurrencies(): iterable
    {
        yield 'empty query' => [
            null,
            3,
            static function (CurrencyListInterface $currencies): void {
                $currencies = $currencies->getCurrencies();
                $currencies = array_map(static fn (CurrencyInterface $currency) => $currency->getId(), $currencies);
                self::assertEqualsCanonicalizing([
                    CurrencyFixture::EUR_ID,
                    CurrencyFixture::USD_ID,
                    CurrencyFixture::BTC_ID,
                ], $currencies);
            },
        ];

        yield 'query "USD" currency' => [
            new CurrencyQuery(
                new CurrencyCodeCriterion(self::USD_CURRENCY_CODE),
            ),
            1,
            static function (CurrencyListInterface $currencies): void {
                $currencies = $currencies->getCurrencies();
                $currencies = array_map(static fn (CurrencyInterface $currency) => $currency->getId(), $currencies);
                self::assertEqualsCanonicalizing([
                    CurrencyFixture::USD_ID,
                ], $currencies);
            },
        ];

        yield 'query enabled currencies' => [
            new CurrencyQuery(
                new IsCurrencyEnabledCriterion(),
            ),
            3,
            static function (CurrencyListInterface $currencies): void {
                $codes = array_map(
                    static fn (CurrencyInterface $currency) => $currency->getCode(),
                    $currencies->getCurrencies()
                );

                self::assertEqualsCanonicalizing(['USD', 'EUR', 'BTC'], $codes);
            },
        ];

        yield 'query with sorting by ID descending' => [
            new CurrencyQuery(
                null,
                [new FieldValueSortClause('id', AbstractSortClause::SORT_DESC)],
            ),
            3,
            static function (CurrencyListInterface $currencies): void {
                $ids = array_map(
                    static fn (CurrencyInterface $currency) => $currency->getId(),
                    $currencies->getCurrencies()
                );

                self::assertSame([3, 2, 1], $ids);
            },
        ];

        yield 'query with sorting by code' => [
            new CurrencyQuery(null, [new CurrencyCode()]),
            3,
            static function (CurrencyListInterface $currencies): void {
                $codes = array_map(
                    static fn (CurrencyInterface $currency) => $currency->getCode(),
                    $currencies->getCurrencies()
                );

                self::assertSame(['BTC', 'EUR', 'USD'], $codes);
            },
        ];
    }

    public function testGetCurrency(): void
    {
        $currency = $this->currencyService->getCurrency(CurrencyFixture::EUR_ID);

        self::assertSame(CurrencyFixture::EUR_ID, $currency->getId());
    }

    public function testGetCurrencyThrowsNotFoundException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage("Could not find 'Ibexa\ProductCatalog\Local\Persistence\Values\Currency' with identifier '42'");

        $this->currencyService->getCurrency(self::NON_EXISTING_CURRENCY_ID);
    }

    public function testGetCurrencyByCode(): void
    {
        $currency = $this->currencyService->getCurrencyByCode(self::USD_CURRENCY_CODE);

        self::assertSame(self::USD_CURRENCY_CODE, $currency->getCode());
    }

    public function testGetCurrencyByCodeThrowsNotFoundException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage("Could not find 'Ibexa\ProductCatalog\Local\Persistence\Values\Currency' with identifier 'non'");

        $this->currencyService->getCurrencyByCode(self::NON_EXISTING_CURRENCY_CODE);
    }

    public function testCreateCurrency(): void
    {
        $struct = self::getCurrencyCreateStruct();

        $createdCurrency = $this->currencyService->createCurrency($struct);

        self::assertSame('foo', $createdCurrency->getCode());
        self::assertSame(2, $createdCurrency->getSubUnits());
        self::assertTrue($createdCurrency->isEnabled());
    }

    public function testCreateCurrencyCodeValidation(): void
    {
        $struct = self::getCurrencyCreateStruct();

        $this->currencyService->createCurrency($struct);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Argument \'struct\' is invalid: Currency with code "foo" already exists');
        $this->currencyService->createCurrency($struct);
    }

    public function testCreateCurrencyThrowsInvalidArgumentException(): void
    {
        $struct = self::getCurrencyCreateStruct(self::TOO_LONG_CURRENCY_CODE);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Argument 'struct' is invalid: Currency code cannot be longer than 3 characters");

        $this->currencyService->createCurrency($struct);
    }

    public function testUpdateCurrency(): void
    {
        $currency = self::getCurrencyService()->getCurrency(CurrencyFixture::USD_ID);

        $struct = new CurrencyUpdateStruct();
        $struct->setCode(self::PLN_CURRENCY_CODE);
        $struct->setSubunits(4);
        $struct->setEnabled(false);

        $updatedCurrency = $this->currencyService->updateCurrency($currency, $struct);

        self::assertSame(self::PLN_CURRENCY_CODE, $updatedCurrency->getCode());
        self::assertSame(4, $updatedCurrency->getSubUnits());
        self::assertFalse($updatedCurrency->isEnabled());
    }

    public function testUpdateCurrencyCodeValidation(): void
    {
        $createStruct = self::getCurrencyCreateStruct();
        $fooCurrency = $this->currencyService->createCurrency($createStruct);

        $createStruct = self::getCurrencyCreateStruct('bar');
        $barCurrency = $this->currencyService->createCurrency($createStruct);

        // Updating the currency with the same code is OK
        $updateStruct = new CurrencyUpdateStruct();
        $updateStruct->setCode('bar');
        $this->currencyService->updateCurrency($barCurrency, $updateStruct);

        $updateStruct = new CurrencyUpdateStruct();
        $updateStruct->setCode('bar');
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Argument \'struct\' is invalid: Currency with code "bar" already exists');
        $this->currencyService->updateCurrency($fooCurrency, $updateStruct);
    }

    public function testUpdateCurrencyThrowsInvalidArgumentException(): void
    {
        $currency = self::getCurrencyService()->getCurrency(CurrencyFixture::USD_ID);

        $struct = new CurrencyUpdateStruct();
        $struct->setCode(self::TOO_LONG_CURRENCY_CODE);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Argument 'struct' is invalid: Currency code cannot be longer than 3 characters");

        $this->currencyService->updateCurrency($currency, $struct);
    }

    public function testDeleteCurrency(): void
    {
        $currency = $this->currencyService->getCurrency(CurrencyFixture::EUR_ID);

        $this->currencyService->deleteCurrency($currency);

        try {
            $this->currencyService->getCurrency(CurrencyFixture::EUR_ID);
            $isDeleted = false;
        } catch (NotFoundException $e) {
            $isDeleted = true;
        }

        self::assertTrue($isDeleted);
    }

    public function testDeleteCurrencyThrowsNotFoundException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage("Could not find 'Ibexa\ProductCatalog\Local\Persistence\Values\Currency' with identifier '42'");

        $this->currencyService->deleteCurrency(
            new Currency(self::NON_EXISTING_CURRENCY_ID, 'foo', 2, true)
        );
    }
}
