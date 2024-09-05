<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Local\Persistence\Legacy\ProductPrice\CustomerGroup;

use Ibexa\ProductCatalog\Local\Persistence\Legacy\CustomerGroup\HandlerInterface;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\ProductPrice\CustomerGroup\Mapper;
use Ibexa\ProductCatalog\Local\Persistence\Values\Currency;
use Ibexa\ProductCatalog\Local\Persistence\Values\CustomerGroup;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\ProductCatalog\Local\Persistence\Legacy\ProductPrice\CustomerGroup\Mapper
 */
final class MapperTest extends TestCase
{
    /**
     * @var \Ibexa\ProductCatalog\Local\Persistence\Legacy\CustomerGroup\HandlerInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private HandlerInterface $customerGroupHandler;

    private Mapper $mapper;

    protected function setUp(): void
    {
        $this->customerGroupHandler = $this->createMock(HandlerInterface::class);
        $this->mapper = new Mapper($this->customerGroupHandler);
    }

    public function testCanHandleResultSet(): void
    {
        self::assertFalse($this->mapper->canHandleResultSet('foo'));
        self::assertTrue($this->mapper->canHandleResultSet('customer_group'));
    }

    public function testHandleResultSet(): void
    {
        $spiCurrency = new Currency(1, 'FOO', 2, true);
        $row = [
            'id' => 777,
            'amount' => '4200',
            'custom_price_amount' => null,
            'custom_price_rule' => null,
            'currency_id' => 888,
            'product_code' => 'PRODUCT_CODE',
            'discriminator' => 'main',
            'customer_group_customer_group_id' => 999,
        ];

        $spiCustomerGroup = new CustomerGroup();
        $this->customerGroupHandler
            ->expects(self::once())
            ->method('find')
            ->with(self::identicalTo(999))
            ->willReturn($spiCustomerGroup);

        $spiPrice = $this->mapper->handleResultSet('customer_group', $row, $spiCurrency);

        self::assertSame(777, $spiPrice->getId());
        self::assertSame('4200', $spiPrice->getAmount());
        self::assertSame($spiCurrency, $spiPrice->getCurrency());
        self::assertSame('PRODUCT_CODE', $spiPrice->getProductCode());
        self::assertSame($spiCustomerGroup, $spiPrice->getCustomerGroup());
    }
}
