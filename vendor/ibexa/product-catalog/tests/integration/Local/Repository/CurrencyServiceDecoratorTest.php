<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Local\Repository;

use Ibexa\Contracts\ProductCatalog\CurrencyServiceDecorator;
use Ibexa\Contracts\ProductCatalog\CurrencyServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\Currency\CurrencyCreateStruct;
use Ibexa\Contracts\ProductCatalog\Values\Currency\CurrencyListInterface;
use Ibexa\Contracts\ProductCatalog\Values\Currency\CurrencyQuery;
use Ibexa\Contracts\ProductCatalog\Values\Currency\CurrencyUpdateStruct;
use Ibexa\Contracts\ProductCatalog\Values\CurrencyInterface;
use PHPUnit\Framework\TestCase;

final class CurrencyServiceDecoratorTest extends TestCase
{
    private const EXAMPLE_CODE = 'EUR';
    private const EXAMPLE_ID = 46;

    /** @var \Ibexa\Contracts\ProductCatalog\CurrencyServiceInterface|\PHPUnit\Framework\MockObject\MockObject */
    private CurrencyServiceInterface $service;

    private CurrencyServiceDecorator $decorator;

    protected function setUp(): void
    {
        $this->service = $this->createMock(CurrencyServiceInterface::class);
        $this->decorator = $this->createDecorator($this->service);
    }

    public function testCreateCurrency(): void
    {
        $struct = new CurrencyCreateStruct('EUR', 2, true);
        $expectedCurrency = $this->createMock(CurrencyInterface::class);

        $this->service
            ->expects(self::once())
            ->method('createCurrency')
            ->with($struct)
            ->willReturn($expectedCurrency);

        self::assertSame(
            $expectedCurrency,
            $this->decorator->createCurrency($struct)
        );
    }

    public function testDeleteCurrency(): void
    {
        $currency = $this->createMock(CurrencyInterface::class);

        $this->service
            ->expects(self::once())
            ->method('deleteCurrency')
            ->with($currency);

        $this->service->deleteCurrency($currency);
    }

    public function testGetCurrency(): void
    {
        $expectedCurrency = $this->createMock(CurrencyInterface::class);

        $this->service
            ->expects(self::once())
            ->method('getCurrency')
            ->with(self::EXAMPLE_ID)
            ->willReturn($expectedCurrency);

        self::assertSame(
            $expectedCurrency,
            $this->decorator->getCurrency(self::EXAMPLE_ID)
        );
    }

    public function testGetCurrencyByCode(): void
    {
        $expectedCurrency = $this->createMock(CurrencyInterface::class);

        $this->service
            ->expects(self::once())
            ->method('getCurrencyByCode')
            ->with(self::EXAMPLE_CODE)
            ->willReturn($expectedCurrency);

        self::assertSame(
            $expectedCurrency,
            $this->decorator->getCurrencyByCode(self::EXAMPLE_CODE)
        );
    }

    public function testFindCurrencies(): void
    {
        $expectedCurrencyList = $this->createMock(CurrencyListInterface::class);
        $query = new CurrencyQuery();

        $this->service
            ->expects(self::once())
            ->method('findCurrencies')
            ->with($query)
            ->willReturn($expectedCurrencyList);

        self::assertSame(
            $expectedCurrencyList,
            $this->decorator->findCurrencies($query)
        );
    }

    public function testUpdateCurrency(): void
    {
        $currency = $this->createMock(CurrencyInterface::class);
        $struct = new CurrencyUpdateStruct();
        $expectedCurrency = $this->createMock(CurrencyInterface::class);

        $this->service
            ->expects(self::once())
            ->method('updateCurrency')
            ->with($currency, $struct)
            ->willReturn($expectedCurrency);

        self::assertSame(
            $expectedCurrency,
            $this->decorator->updateCurrency($currency, $struct)
        );
    }

    private function createDecorator(CurrencyServiceInterface $service): CurrencyServiceDecorator
    {
        return new class($service) extends CurrencyServiceDecorator {
            // Empty decorator implementation
        };
    }
}
