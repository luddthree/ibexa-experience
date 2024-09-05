<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Local\Repository;

use Ibexa\Contracts\ProductCatalog\Local\LocalProductTypeServiceDecorator;
use Ibexa\Contracts\ProductCatalog\Local\LocalProductTypeServiceInterface;
use Ibexa\Contracts\ProductCatalog\Local\Values\ProductType\ProductTypeCreateStruct;
use Ibexa\Contracts\ProductCatalog\Local\Values\ProductType\ProductTypeUpdateStruct;
use Ibexa\Contracts\ProductCatalog\Values\ProductType\ProductTypeListInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductType\ProductTypeQuery;
use Ibexa\Contracts\ProductCatalog\Values\ProductTypeInterface;
use PHPUnit\Framework\TestCase;

final class LocalProductTypeServiceDecoratorTest extends TestCase
{
    private const EXAMPLE_IDENTIFIER = 'foo';
    private const EXAMPLE_LANGUAGE = 'eng-GB';

    /** @var \Ibexa\Contracts\ProductCatalog\Local\LocalProductTypeServiceInterface|\PHPUnit\Framework\MockObject\MockObject */
    private LocalProductTypeServiceInterface $service;

    private LocalProductTypeServiceDecorator $decorator;

    protected function setUp(): void
    {
        $this->service = $this->createMock(LocalProductTypeServiceInterface::class);
        $this->decorator = $this->createDecorator($this->service);
    }

    public function testCreateProductType(): void
    {
        $createStruct = $this->createMock(ProductTypeCreateStruct::class);
        $expectedProductType = $this->createMock(ProductTypeInterface::class);

        $this->service
            ->expects(self::once())
            ->method('createProductType')
            ->with($createStruct)
            ->willReturn($expectedProductType);

        $actualProductType = $this->decorator->createProductType($createStruct);

        self::assertSame($expectedProductType, $actualProductType);
    }

    public function testNewProductTypeCreateStruct(): void
    {
        $expectedCreateStruct = $this->createMock(ProductTypeCreateStruct::class);

        $this->service
            ->expects(self::once())
            ->method('newProductTypeCreateStruct')
            ->with(self::EXAMPLE_IDENTIFIER)
            ->willReturn($expectedCreateStruct);

        $actualCreateStruct = $this->decorator->newProductTypeCreateStruct(
            self::EXAMPLE_IDENTIFIER,
            self::EXAMPLE_LANGUAGE
        );

        self::assertSame($expectedCreateStruct, $actualCreateStruct);
    }

    public function testNewProductTypeUpdateStruct(): void
    {
        $productType = $this->createMock(ProductTypeInterface::class);
        $expectedUpdateStruct = $this->createMock(ProductTypeUpdateStruct::class);

        $this->service
            ->expects(self::once())
            ->method('newProductTypeUpdateStruct')
            ->with($productType)
            ->willReturn($expectedUpdateStruct);

        $actualUpdateStruct = $this->decorator->newProductTypeUpdateStruct($productType);

        self::assertSame($expectedUpdateStruct, $actualUpdateStruct);
    }

    public function testUpdateProductType(): void
    {
        $updateStruct = $this->createMock(ProductTypeUpdateStruct::class);
        $expectedProductType = $this->createMock(ProductTypeInterface::class);

        $this->service
            ->expects(self::once())
            ->method('updateProductType')
            ->with($updateStruct)
            ->willReturn($expectedProductType);

        $actualProductType = $this->decorator->updateProductType($updateStruct);

        self::assertSame($expectedProductType, $actualProductType);
    }

    public function testDeleteProductType(): void
    {
        $productType = $this->createMock(ProductTypeInterface::class);

        $this->service
            ->expects(self::once())
            ->method('deleteProductType')
            ->with($productType);

        $this->decorator->deleteProductType($productType);
    }

    public function testDeleteProductTypeTranslation(): void
    {
        $productType = $this->createMock(ProductTypeInterface::class);

        $this->service
            ->expects(self::once())
            ->method('deleteProductTypeTranslation')
            ->with($productType, 'eng-GB');

        $this->decorator->deleteProductTypeTranslation($productType, 'eng-GB');
    }

    public function testIsProductTypeUsed(): void
    {
        $productType = $this->createMock(ProductTypeInterface::class);

        $this->service
            ->expects(self::once())
            ->method('isProductTypeUsed')
            ->with($productType)
            ->willReturn(true);

        self::assertTrue($this->decorator->isProductTypeUsed($productType));
    }

    public function testGetProductType(): void
    {
        $expectedProductType = $this->createMock(ProductTypeInterface::class);

        $this->service
            ->expects(self::once())
            ->method('getProductType')
            ->with(self::EXAMPLE_IDENTIFIER)
            ->willReturn($expectedProductType);

        $actualProductType = $this->decorator->getProductType(self::EXAMPLE_IDENTIFIER);

        self::assertSame($expectedProductType, $actualProductType);
    }

    public function testFindProductTypes(): void
    {
        $query = new ProductTypeQuery();
        $expectedProductTypeList = $this->createMock(ProductTypeListInterface::class);

        $this->service
            ->expects(self::once())
            ->method('findProductTypes')
            ->with($query)
            ->willReturn($expectedProductTypeList);

        $actualProductTypeList = $this->decorator->findProductTypes($query);

        self::assertSame($expectedProductTypeList, $actualProductTypeList);
    }

    private function createDecorator(LocalProductTypeServiceInterface $service): LocalProductTypeServiceDecorator
    {
        return new class($service) extends LocalProductTypeServiceDecorator {
            // Empty decorator implementation
        };
    }
}
