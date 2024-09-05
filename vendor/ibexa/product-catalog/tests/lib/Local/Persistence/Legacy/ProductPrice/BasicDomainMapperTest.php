<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Local\Persistence\Legacy\ProductPrice;

use Ibexa\Contracts\ProductCatalog\Values\CurrencyInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\ProductPrice\BasicDomainMapper;
use Ibexa\ProductCatalog\Local\Persistence\Values\AbstractProductPrice;
use Ibexa\ProductCatalog\Local\Persistence\Values\Currency;
use Ibexa\ProductCatalog\Local\Persistence\Values\ProductPrice;
use Money\Currency as MoneyCurrency;
use Money\Money;
use Money\MoneyFormatter;
use Money\MoneyParser;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\ProductCatalog\Local\Persistence\Legacy\ProductPrice\BasicDomainMapper
 */
final class BasicDomainMapperTest extends TestCase
{
    private BasicDomainMapper $mapper;

    protected function setUp(): void
    {
        $this->mapper = new BasicDomainMapper();
    }

    public function testCanHandleSpiPrice(): void
    {
        self::assertFalse($this->mapper->canMapSpiPrice($this->createMock(AbstractProductPrice::class)));

        $spiCurrency = new Currency(1, 'FOO', 2, true);
        $spiPrice = new ProductPrice(1, '4200', $spiCurrency, 'FOO');

        self::assertTrue($this->mapper->canMapSpiPrice($spiPrice));
    }

    public function testHandleSpiPrice(): void
    {
        $product = $this->createMock(ProductInterface::class);
        $currency = $this->createMock(CurrencyInterface::class);
        $spiCurrency = new Currency(1, 'FOO', 2, true);
        $spiPrice = new ProductPrice(1, '4200', $spiCurrency, 'FOO');

        $moneyCurrency = new MoneyCurrency('FOO');
        $money = new Money('4200', $moneyCurrency);

        $moneyFormatter = $this->createMock(MoneyFormatter::class);
        $moneyFormatter->expects(self::once())
            ->method('format')
            ->with($money)
            ->willReturn('42.00');

        $moneyParser = $this->createMock(MoneyParser::class);
        $moneyParser->expects(self::never())
            ->method('parse');

        $price = $this->mapper->mapSpiPrice(
            $moneyFormatter,
            $moneyParser,
            $product,
            $currency,
            $spiPrice,
            $money,
        );
        self::assertSame(1, $price->getId());
        self::assertSame('42.00', $price->getAmount());
        self::assertSame($currency, $price->getCurrency());
        self::assertSame($product, $price->getProduct());
        self::assertSame($money, $price->getMoney());
    }
}
