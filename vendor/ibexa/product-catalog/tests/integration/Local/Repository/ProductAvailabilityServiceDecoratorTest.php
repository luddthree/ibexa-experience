<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Local\Repository;

use Ibexa\Contracts\ProductCatalog\ProductAvailabilityServiceDecorator;
use Ibexa\Contracts\ProductCatalog\ProductAvailabilityServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\Availability\AvailabilityContextInterface;
use Ibexa\Contracts\ProductCatalog\Values\Availability\AvailabilityInterface;
use Ibexa\Contracts\ProductCatalog\Values\Availability\ProductAvailabilityCreateStruct;
use Ibexa\Contracts\ProductCatalog\Values\Availability\ProductAvailabilityUpdateStruct;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use PHPUnit\Framework\TestCase;

final class ProductAvailabilityServiceDecoratorTest extends TestCase
{
    /** @var \Ibexa\Contracts\ProductCatalog\ProductAvailabilityServiceInterface|\PHPUnit\Framework\MockObject\MockObject */
    private ProductAvailabilityServiceInterface $service;

    private ProductAvailabilityServiceDecorator $decorator;

    protected function setUp(): void
    {
        $this->service = $this->createMock(ProductAvailabilityServiceInterface::class);
        $this->decorator = $this->createDecorator($this->service);
    }

    public function testGetAvailability(): void
    {
        $product = $this->createMock(ProductInterface::class);
        $context = $this->createMock(AvailabilityContextInterface::class);
        $expectedAvailability = $this->createMock(AvailabilityInterface::class);

        $this->service
            ->expects(self::once())
            ->method('getAvailability')
            ->with($product, $context)
            ->willReturn($expectedAvailability);

        self::assertSame(
            $expectedAvailability,
            $this->decorator->getAvailability($product, $context)
        );
    }

    public function testHasAvailability(): void
    {
        $product = $this->createMock(ProductInterface::class);

        $this->service
            ->expects(self::once())
            ->method('hasAvailability')
            ->with($product)
            ->willReturn(true);

        self::assertTrue($this->decorator->hasAvailability($product));
    }

    public function testCreateProductAvailability(): void
    {
        $createStruct = new ProductAvailabilityCreateStruct(
            $this->createMock(ProductInterface::class),
            true,
            false,
            1000
        );

        $expectedAvailability = $this->createMock(AvailabilityInterface::class);

        $this->service
            ->expects(self::once())
            ->method('createProductAvailability')
            ->with($createStruct)
            ->willReturn($expectedAvailability);

        self::assertSame(
            $expectedAvailability,
            $this->decorator->createProductAvailability($createStruct)
        );
    }

    public function testUpdateProductAvailability(): void
    {
        $updatedStruct = new ProductAvailabilityUpdateStruct(
            $this->createMock(ProductInterface::class),
        );

        $expectedAvailability = $this->createMock(AvailabilityInterface::class);

        $this->service
            ->expects(self::once())
            ->method('updateProductAvailability')
            ->with($updatedStruct)
            ->willReturn($expectedAvailability);

        self::assertSame(
            $expectedAvailability,
            $this->decorator->updateProductAvailability($updatedStruct)
        );
    }

    public function testIncreaseProductAvailability(): void
    {
        $product = $this->createMock(ProductInterface::class);
        $expectedAvailability = $this->createMock(AvailabilityInterface::class);

        $this->service
            ->expects(self::once())
            ->method('increaseProductAvailability')
            ->with($product, 1000)
            ->willReturn($expectedAvailability);

        self::assertSame(
            $expectedAvailability,
            $this->decorator->increaseProductAvailability($product, 1000)
        );
    }

    public function testDecreaseProductAvailability(): void
    {
        $product = $this->createMock(ProductInterface::class);
        $expectedAvailability = $this->createMock(AvailabilityInterface::class);

        $this->service
            ->expects(self::once())
            ->method('decreaseProductAvailability')
            ->with($product, 1000)
            ->willReturn($expectedAvailability);

        self::assertSame(
            $expectedAvailability,
            $this->decorator->decreaseProductAvailability($product, 1000)
        );
    }

    public function testDeleteProductAvailability(): void
    {
        $product = $this->createMock(ProductInterface::class);

        $this->service
            ->expects(self::once())
            ->method('deleteProductAvailability')
            ->with($product);

        $this->decorator->deleteProductAvailability($product);
    }

    private function createDecorator(ProductAvailabilityServiceInterface $service): ProductAvailabilityServiceDecorator
    {
        return new class($service) extends ProductAvailabilityServiceDecorator {
            // Empty decorator implementation
        };
    }
}
