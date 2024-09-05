<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\CorporateAccount\Commerce\Orders\Legacy;

use Ibexa\Bundle\Commerce\Checkout\Entity\Basket;
use Ibexa\Bundle\Commerce\LocalOrderManagement\Entity\Invoice as InvoiceEntity;
use Ibexa\Bundle\Commerce\LocalOrderManagement\Service\InvoiceService;
use Ibexa\CorporateAccount\Commerce\Orders\Legacy\Invoice;
use Ibexa\CorporateAccount\Commerce\Orders\Legacy\InvoiceFactory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class InvoiceFactoryTest extends TestCase
{
    private const EXAMPLE_BASKET_ID = 674;
    private const EXAMPLE_INVOICE_SYMBOL = 'FV/11/000151/22';
    private const EXAMPLE_INVOICE_URL = 'https://example.com/invoice/FV/11/000151/22';

    /** @var \Ibexa\Bundle\Commerce\LocalOrderManagement\Service\InvoiceService&\PHPUnit\Framework\MockObject\MockObject */
    private InvoiceService $invoiceService;

    /** @var \Symfony\Component\Routing\Generator\UrlGeneratorInterface&\PHPUnit\Framework\MockObject\MockObject */
    private UrlGeneratorInterface $urlGenerator;

    protected function setUp(): void
    {
        $this->invoiceService = $this->createMock(InvoiceService::class);

        $this->urlGenerator = $this->createMock(UrlGeneratorInterface::class);
    }

    public function testCreateInvoiceForBasket(): void
    {
        $basket = $this->createExampleBasket(self::EXAMPLE_BASKET_ID);

        $this->urlGenerator
            ->method('generate')
            ->willReturn(self::EXAMPLE_INVOICE_URL);

        $this->invoiceService
            ->method('getInvoiceByBasketId')
            ->with(self::EXAMPLE_BASKET_ID)
            ->willReturn($this->createInvoiceEntityWithSymbol(self::EXAMPLE_INVOICE_SYMBOL));

        $factory = new InvoiceFactory($this->invoiceService, $this->urlGenerator);

        self::assertEquals(
            new Invoice(
                self::EXAMPLE_INVOICE_SYMBOL,
                self::EXAMPLE_INVOICE_URL
            ),
            $factory->createInvoiceForBasket($basket)
        );
    }

    public function testCreateInvoiceForBasketWithMissingCorrespondingEntity(): void
    {
        $basket = $this->createExampleBasket(self::EXAMPLE_BASKET_ID);

        $this->invoiceService
            ->method('getInvoiceByBasketId')
            ->with(self::EXAMPLE_BASKET_ID)
            ->willReturn(null);

        $factory = new InvoiceFactory($this->invoiceService, $this->urlGenerator);

        self::assertNull($factory->createInvoiceForBasket($basket));
    }

    public function testCreateInvoiceReturnsNullIfInvoiceServiceIsUnavailale(): void
    {
        $basket = $this->createExampleBasket(self::EXAMPLE_BASKET_ID);
        $factory = new InvoiceFactory(null, $this->urlGenerator);

        self::assertNull($factory->createInvoiceForBasket($basket));
    }

    private function createInvoiceEntityWithSymbol(string $symbol): InvoiceEntity
    {
        $entity = $this->createMock(InvoiceEntity::class);
        $entity->method('getInvoiceNumber')->willReturn($symbol);

        return $entity;
    }

    protected function createExampleBasket(int $basketId): Basket
    {
        $basket = $this->createMock(Basket::class);
        $basket->method('getBasketId')->willReturn($basketId);

        return $basket;
    }
}
