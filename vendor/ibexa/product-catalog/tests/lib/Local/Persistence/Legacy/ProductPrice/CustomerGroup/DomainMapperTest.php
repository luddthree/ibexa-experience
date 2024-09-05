<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Local\Persistence\Legacy\ProductPrice\CustomerGroup;

use Ibexa\Contracts\ProductCatalog\Values\CurrencyInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\CustomerGroup\DomainMapperInterface as CustomerGroupDomainMapperInterface;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\ProductPrice\CustomerGroup\DomainMapper;
use Ibexa\ProductCatalog\Local\Persistence\Values\AbstractProductPrice;
use Ibexa\ProductCatalog\Local\Persistence\Values\Currency;
use Ibexa\ProductCatalog\Local\Persistence\Values\CustomerGroup;
use Ibexa\ProductCatalog\Local\Persistence\Values\ProductPrice\CustomerGroupPrice;
use Money\Currency as MoneyCurrency;
use Money\Money;
use Money\MoneyFormatter;
use Money\MoneyParser;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\ProductCatalog\Local\Persistence\Legacy\ProductPrice\CustomerGroup\DomainMapper
 */
final class DomainMapperTest extends TestCase
{
    /** @var \Ibexa\ProductCatalog\Local\Persistence\Legacy\CustomerGroup\DomainMapperInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $customerGroupDomainMapper;

    private DomainMapper $mapper;

    protected function setUp(): void
    {
        $this->customerGroupDomainMapper = $this->createMock(CustomerGroupDomainMapperInterface::class);
        $this->mapper = new DomainMapper($this->customerGroupDomainMapper);
    }

    public function testCanHandleSpiPrice(): void
    {
        $spiPrice = $this->createMock(AbstractProductPrice::class);
        self::assertFalse($this->mapper->canMapSpiPrice($spiPrice));

        $spiCurrency = new Currency(1, 'FOO', 2, true);
        $spiPrice = new CustomerGroupPrice(
            1,
            '4200',
            $spiCurrency,
            'FOO',
            null,
            null,
            new CustomerGroup(),
        );
        self::assertTrue($this->mapper->canMapSpiPrice($spiPrice));
    }

    public function testHandleSpiPrice(): void
    {
        $product = $this->createMock(ProductInterface::class);
        $currency = $this->createMock(CurrencyInterface::class);
        $spiCurrency = new Currency(1, 'FOO', 2, true);
        $customerGroupSpi = new CustomerGroup([
            'id' => 1,
            'identifier' => 'foo',
        ]);
        $spiPrice = new CustomerGroupPrice(
            1,
            '4200',
            $spiCurrency,
            'FOO',
            null,
            null,
            $customerGroupSpi,
        );

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

        $this->customerGroupDomainMapper
            ->expects(self::once())
            ->method('createFromSpi')
            ->with(self::identicalTo($customerGroupSpi));

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
