<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\Form\Data\Product\Price;

use Ibexa\Bundle\ProductCatalog\Form\Data\Price\Update\ProductPriceUpdateData;
use Ibexa\Contracts\ProductCatalog\Values\CurrencyInterface;
use Ibexa\Contracts\ProductCatalog\Values\PriceInterface;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\Bundle\ProductCatalog\Form\Data\Price\Update\ProductPriceUpdateData
 */
final class ProductPriceUpdateDataTest extends TestCase
{
    public function testCreatesItselfFromPrice(): void
    {
        $price = $this->getConfiguredPriceMock();

        $data = new ProductPriceUpdateData($price);

        self::assertSame(42, $data->getId());
        self::assertSame('42', $data->getBasePrice());
        self::assertSame('BTC', $data->getCurrency()->getCode());
    }

    private function getConfiguredPriceMock(): PriceInterface
    {
        $price = $this->createMock(PriceInterface::class);

        $currency = $this->createMock(CurrencyInterface::class);
        $currency
            ->method('getCode')
            ->willReturn('BTC');

        $price
            ->expects(self::atLeastOnce())
            ->method('getBaseAmount')
            ->willReturn('42');

        $price
            ->expects(self::atLeastOnce())
            ->method('getCurrency')
            ->willReturn($currency);

        $price
            ->expects(self::atLeastOnce())
            ->method('getId')
            ->willReturn(42);

        return $price;
    }
}
