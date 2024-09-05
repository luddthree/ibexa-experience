<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Local\Repository;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\ProductCatalog\Local\LocalProductServiceDecorator;
use Ibexa\Contracts\ProductCatalog\Local\LocalProductServiceInterface;
use Ibexa\Contracts\ProductCatalog\Local\Values\Product\ProductCreateStruct;
use Ibexa\Contracts\ProductCatalog\Local\Values\Product\ProductUpdateStruct;
use Ibexa\Contracts\ProductCatalog\Values\LanguageSettings;
use Ibexa\Contracts\ProductCatalog\Values\Product\ProductListInterface;
use Ibexa\Contracts\ProductCatalog\Values\Product\ProductQuery;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductTypeInterface;
use PHPUnit\Framework\TestCase;

final class LocalProductServiceDecoratorTest extends TestCase
{
    private const EXAMPLE_LANGUAGE_CODE = 'eng-GB';
    private const EXAMPLE_PRODUCT_CODE = '0001';

    /** @var \Ibexa\Contracts\ProductCatalog\Local\LocalProductServiceInterface|\PHPUnit\Framework\MockObject\MockObject */
    private LocalProductServiceInterface $service;

    private LocalProductServiceDecorator $decorator;

    protected function setUp(): void
    {
        $this->service = $this->createMock(LocalProductServiceInterface::class);
        $this->decorator = $this->createDecorator($this->service);
    }

    public function testCreateProduct(): void
    {
        $createStruct = $this->createMock(ProductCreateStruct::class);
        $expectedProduct = $this->createMock(ProductInterface::class);

        $this->service
            ->expects(self::once())
            ->method('createProduct')
            ->with($createStruct)
            ->willReturn($expectedProduct);

        $actualProduct = $this->decorator->createProduct($createStruct);

        self::assertSame($expectedProduct, $actualProduct);
    }

    public function testNewProductCreateStruct(): void
    {
        $expectedCreateStruct = $this->createMock(ProductCreateStruct::class);

        $productType = $this->createMock(ProductTypeInterface::class);

        $this->service
            ->expects(self::once())
            ->method('newProductCreateStruct')
            ->with($productType, self::EXAMPLE_LANGUAGE_CODE)
            ->willReturn($expectedCreateStruct);

        $actualCreateStruct = $this->decorator->newProductCreateStruct($productType, self::EXAMPLE_LANGUAGE_CODE);

        self::assertSame($expectedCreateStruct, $actualCreateStruct);
    }

    public function testNewProductUpdateStruct(): void
    {
        $product = $this->createMock(ProductInterface::class);
        $expectedUpdateStruct = $this->createMock(ProductUpdateStruct::class);

        $this->service
            ->expects(self::once())
            ->method('newProductUpdateStruct')
            ->with($product)
            ->willReturn($expectedUpdateStruct);

        $actualUpdateStruct = $this->decorator->newProductUpdateStruct($product);

        self::assertSame($expectedUpdateStruct, $actualUpdateStruct);
    }

    public function testUpdateProduct(): void
    {
        $updateStruct = $this->createMock(ProductUpdateStruct::class);
        $expectedProduct = $this->createMock(ProductInterface::class);

        $this->service
            ->expects(self::once())
            ->method('updateProduct')
            ->with($updateStruct)
            ->willReturn($expectedProduct);

        $actualProduct = $this->decorator->updateProduct($updateStruct);

        self::assertSame($expectedProduct, $actualProduct);
    }

    public function testDeleteProduct(): void
    {
        $product = $this->createMock(ProductInterface::class);

        $this->service
            ->expects(self::once())
            ->method('deleteProduct')
            ->with($product);

        $this->decorator->deleteProduct($product);
    }

    public function testGetProduct(): void
    {
        $expectedProduct = $this->createMock(ProductInterface::class);
        $languageSettings = new LanguageSettings();

        $this->service
            ->expects(self::once())
            ->method('getProduct')
            ->with(self::EXAMPLE_PRODUCT_CODE, $languageSettings)
            ->willReturn($expectedProduct);

        $actualProduct = $this->decorator->getProduct(self::EXAMPLE_PRODUCT_CODE, $languageSettings);

        self::assertSame($expectedProduct, $actualProduct);
    }

    public function testGetProductFromContent(): void
    {
        $content = $this->createMock(Content::class);
        $expectedProduct = $this->createMock(ProductInterface::class);

        $this->service
            ->expects(self::once())
            ->method('getProductFromContent')
            ->with($content)
            ->willReturn($expectedProduct);

        $actualProduct = $this->decorator->getProductFromContent($content);

        self::assertSame($expectedProduct, $actualProduct);
    }

    public function testIsProduct(): void
    {
        $content = $this->createMock(Content::class);

        $this->service
            ->expects(self::once())
            ->method('isProduct')
            ->with($content)
            ->willReturn(true);

        self::assertTrue($this->decorator->isProduct($content));
    }

    public function testFindProducts(): void
    {
        $query = new ProductQuery();
        $languageSettings = new LanguageSettings();
        $expectedProductList = $this->createMock(ProductListInterface::class);

        $this->service
            ->expects(self::once())
            ->method('findProducts')
            ->with($query, $languageSettings)
            ->willReturn($expectedProductList);

        $actualProductList = $this->decorator->findProducts($query, $languageSettings);

        self::assertSame($expectedProductList, $actualProductList);
    }

    private function createDecorator(LocalProductServiceInterface $service): LocalProductServiceDecorator
    {
        return new class($service) extends LocalProductServiceDecorator {
            // Empty decorator implementation
        };
    }
}
