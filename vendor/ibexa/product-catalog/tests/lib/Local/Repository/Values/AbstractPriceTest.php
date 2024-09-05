<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Local\Repository\Values;

use Ibexa\Contracts\ProductCatalog\Values\CurrencyInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Ibexa\ProductCatalog\Local\Repository\Values\AbstractPrice;
use Money\Money;
use Money\MoneyFormatter;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\ProductCatalog\Local\Repository\Values\AbstractPrice
 */
final class AbstractPriceTest extends TestCase
{
    public function testReturnBasePriceWhenNoCustomRules(): void
    {
        $baseMoney = Money::EUR(10);
        $moneyFormatter = $this->createMock(MoneyFormatter::class);
        $moneyFormatter->expects(self::exactly(2))
            ->method('format')
            ->with(self::identicalTo($baseMoney))
            ->willReturnCallback(static fn (Money $money) => $money->getAmount());

        $price = new class(
            $moneyFormatter,
            1,
            $this->createMock(ProductInterface::class),
            $this->createMock(CurrencyInterface::class),
            $baseMoney,
        ) extends AbstractPrice {
        };

        self::assertSame('10', $price->getBaseAmount());
        self::assertSame($baseMoney, $price->getBaseMoney());

        self::assertSame('10', $price->getAmount());
        self::assertSame($baseMoney, $price->getMoney());
    }
}
