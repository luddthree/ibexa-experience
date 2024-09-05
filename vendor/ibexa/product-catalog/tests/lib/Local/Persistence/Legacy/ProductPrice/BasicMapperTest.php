<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Local\Persistence\Legacy\ProductPrice;

use Ibexa\ProductCatalog\Local\Persistence\Legacy\ProductPrice\BasicMapper;
use Ibexa\ProductCatalog\Local\Persistence\Values\Currency;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\ProductCatalog\Local\Persistence\Legacy\ProductPrice\BasicMapper
 */
final class BasicMapperTest extends TestCase
{
    private BasicMapper $mapper;

    protected function setUp(): void
    {
        $this->mapper = new BasicMapper();
    }

    public function testCanHandleResultSet(): void
    {
        self::assertFalse($this->mapper->canHandleResultSet('foo'));
        self::assertTrue($this->mapper->canHandleResultSet('main'));
    }

    public function testHandleResultSet(): void
    {
        $row = [
            'id' => 1,
            'amount' => '4200',
            'custom_price_amount' => null,
            'custom_price_rule' => null,
            'currency_id' => 1,
            'product_code' => 'PRODUCT_CODE',
            'discriminator' => 'main',
        ];

        $currency = new Currency(1, 'CURRENCY_CODE', 2, true);

        $spiPrice = $this->mapper->handleResultSet('main', $row, $currency);
        self::assertSame(1, $spiPrice->getId());
        self::assertSame($currency, $spiPrice->getCurrency());
        self::assertSame('4200', $spiPrice->getAmount());
        self::assertSame('PRODUCT_CODE', $spiPrice->getProductCode());
    }
}
