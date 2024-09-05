<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Local\Repository\Values\Price;

use Ibexa\Contracts\ProductCatalog\Values\CurrencyInterface;
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroupInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Ibexa\ProductCatalog\Local\Repository\Values\Price\CustomGroupPrice;
use Money\Money;
use Money\MoneyFormatter;
use PHPUnit\Framework\TestCase;

final class CustomGroupPriceTest extends TestCase
{
    public function testReturnBasePriceWhenNoCustomRules(): void
    {
        $baseMoney = Money::EUR(10);
        $moneyFormatter = $this->createMock(MoneyFormatter::class);
        $moneyFormatter->expects(self::exactly(2))
            ->method('format')
            ->willReturnCallback(static fn (Money $money) => $money->getAmount());

        $price = new CustomGroupPrice(
            $moneyFormatter,
            1,
            $this->createMock(ProductInterface::class),
            $this->createMock(CurrencyInterface::class),
            $baseMoney,
            null,
            null,
            $this->createMock(CustomerGroupInterface::class),
        );

        self::assertSame('10', $price->getBaseAmount());
        self::assertSame($baseMoney, $price->getBaseMoney());

        self::assertNull($price->getCustomPrice());
        self::assertNull($price->getCustomPriceAmount());

        self::assertSame('10', $price->getAmount());
        self::assertSame($baseMoney, $price->getMoney());
    }

    public function testReturnCustomPriceWhenCustomRuleExists(): void
    {
        $baseMoney = Money::EUR(10);
        $customMoney = Money::EUR(20);
        $moneyFormatter = $this->createMock(MoneyFormatter::class);
        $moneyFormatter->expects(self::exactly(3))
            ->method('format')
            ->willReturnCallback(static fn (Money $money) => $money->getAmount());

        $price = new CustomGroupPrice(
            $moneyFormatter,
            1,
            $this->createMock(ProductInterface::class),
            $this->createMock(CurrencyInterface::class),
            $baseMoney,
            $customMoney,
            '100',
            $this->createMock(CustomerGroupInterface::class),
        );

        self::assertSame('10', $price->getBaseAmount());
        self::assertSame($baseMoney, $price->getBaseMoney());

        self::assertSame('20', $price->getCustomPriceAmount());
        self::assertSame($customMoney, $price->getCustomPrice());

        self::assertSame('20', $price->getAmount());
        self::assertSame($customMoney, $price->getMoney());
    }
}
