<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Local\Repository;

use Ibexa\Contracts\ProductCatalog\ProductPriceServiceDecorator;
use Ibexa\Contracts\ProductCatalog\ProductPriceServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\CurrencyInterface;
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroupInterface;
use Ibexa\Contracts\ProductCatalog\Values\CustomPriceAwareInterface;
use Ibexa\Contracts\ProductCatalog\Values\Price\Create\Struct\ProductPriceCreateStructInterface;
use Ibexa\Contracts\ProductCatalog\Values\Price\Delete\Struct\ProductPriceDeleteStructInterface;
use Ibexa\Contracts\ProductCatalog\Values\Price\PriceListInterface;
use Ibexa\Contracts\ProductCatalog\Values\Price\Update\Struct\ProductPriceUpdateStructInterface;
use Ibexa\Contracts\ProductCatalog\Values\PriceInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use PHPUnit\Framework\TestCase;

final class ProductPriceServiceDecoratorTest extends TestCase
{
    private const EXAMPLE_CODE = 'XA-740';
    private const EXAMPLE_ID = 52;

    /** @var \Ibexa\Contracts\ProductCatalog\ProductPriceServiceInterface|\PHPUnit\Framework\MockObject\MockObject */
    private ProductPriceServiceInterface $service;

    private ProductPriceServiceDecorator $decorator;

    protected function setUp(): void
    {
        $this->service = $this->createMock(ProductPriceServiceInterface::class);
        $this->decorator = $this->createDecorator($this->service);
    }

    public function testExecute(): void
    {
        $structs = [
            $this->createMock(ProductPriceCreateStructInterface::class),
            $this->createMock(ProductPriceDeleteStructInterface::class),
            $this->createMock(ProductPriceUpdateStructInterface::class),
        ];

        $this->service
            ->expects(self::once())
            ->method('execute')
            ->with($structs);

        $this->decorator->execute($structs);
    }

    public function testDeletePrice(): void
    {
        $struct = $this->createMock(ProductPriceDeleteStructInterface::class);

        $this->service
            ->expects(self::once())
            ->method('deletePrice')
            ->with($struct);

        $this->decorator->deletePrice($struct);
    }

    public function testFindPricesByProductCode(): void
    {
        $expectedResult = $this->createMock(PriceListInterface::class);

        $this->service
            ->expects(self::once())
            ->method('findPricesByProductCode')
            ->with(self::EXAMPLE_CODE)
            ->willReturn($expectedResult);

        $actualResult = $this->decorator->findPricesByProductCode(self::EXAMPLE_CODE);

        self::assertSame($expectedResult, $actualResult);
    }

    public function testCreateProductPrice(): void
    {
        $createStruct = $this->createMock(ProductPriceCreateStructInterface::class);
        $expectedResult = $this->createMock(PriceInterface::class);

        $this->service
            ->expects(self::once())
            ->method('createProductPrice')
            ->with($createStruct)
            ->willReturn($expectedResult);

        $actualResult = $this->decorator->createProductPrice($createStruct);

        self::assertSame($expectedResult, $actualResult);
    }

    public function testUpdateProductPrice(): void
    {
        $updateStruct = $this->createMock(ProductPriceUpdateStructInterface::class);
        $expectedResult = $this->createMock(PriceInterface::class);

        $this->service
            ->expects(self::once())
            ->method('updateProductPrice')
            ->with($updateStruct)
            ->willReturn($expectedResult);

        $actualResult = $this->decorator->updateProductPrice($updateStruct);

        self::assertSame($expectedResult, $actualResult);
    }

    public function testFindOneForCustomerGroup(): void
    {
        $price = $this->createMock(PriceInterface::class);
        $customerGroup = $this->createMock(CustomerGroupInterface::class);
        $expectedResult = $this->createMock(CustomPriceAwareInterface::class);

        $this->service
            ->expects(self::once())
            ->method('findOneForCustomerGroup')
            ->with($price, $customerGroup)
            ->willReturn($expectedResult);

        $actualResult = $this->decorator->findOneForCustomerGroup($price, $customerGroup);

        self::assertSame($expectedResult, $actualResult);
    }

    public function testGetPriceByProductAndCurrency(): void
    {
        $product = $this->createMock(ProductInterface::class);
        $currency = $this->createMock(CurrencyInterface::class);
        $expectedResult = $this->createMock(PriceInterface::class);

        $this->service
            ->expects(self::once())
            ->method('getPriceByProductAndCurrency')
            ->with($product, $currency)
            ->willReturn($expectedResult);

        $actualResult = $this->decorator->getPriceByProductAndCurrency($product, $currency);

        self::assertSame($expectedResult, $actualResult);
    }

    public function testGetPriceById(): void
    {
        $expectedResult = $this->createMock(PriceInterface::class);

        $this->service
            ->expects(self::once())
            ->method('getPriceById')
            ->with(self::EXAMPLE_ID)
            ->willReturn($expectedResult);

        $actualResult = $this->decorator->getPriceById(self::EXAMPLE_ID);

        self::assertSame($expectedResult, $actualResult);
    }

    private function createDecorator(ProductPriceServiceInterface $service): ProductPriceServiceDecorator
    {
        return new class($service) extends ProductPriceServiceDecorator {
            // Empty decorator implementation
        };
    }
}
