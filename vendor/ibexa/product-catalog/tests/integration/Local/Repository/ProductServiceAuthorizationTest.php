<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Local\Repository;

use Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException;
use Ibexa\Contracts\ProductCatalog\Local\Values\Product\ProductCreateStruct;
use Ibexa\Contracts\ProductCatalog\Local\Values\Product\ProductUpdateStruct;
use Ibexa\Contracts\ProductCatalog\Values\Product\ProductQuery;

/**
 * @covers \Ibexa\ProductCatalog\Local\Repository\ProductService
 *
 * @group product-service
 */
final class ProductServiceAuthorizationTest extends BaseProductServiceTest
{
    public function testAddProductThrowsUnauthorizedException(): void
    {
        $productService = self::getLocalProductService();

        $productCreateStruct = $productService->newProductCreateStruct(
            self::getProductTypeService()->getProductType(self::TEST_PRODUCT_TYPE_IDENTIFIER_TROUSERS),
            'eng-GB'
        );

        self::assertInstanceOf(
            ProductCreateStruct::class,
            $productCreateStruct,
            'Local implementation of ProductService returns a specific concrete ' . ProductCreateStruct::class
        );
        $productCreateStruct->setCode('code');
        $productCreateStruct->setField('name', 'foo');

        self::setAnonymousUser();

        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessageMatches('/\'create\' \'product\'/');

        $productService->createProduct($productCreateStruct);
    }

    public function testGetProductThrowsUnauthorizedException(): void
    {
        $productService = self::getLocalProductService();
        self::setAnonymousUser();

        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessageMatches('/\'view\' \'product\'/');

        $productService->getProduct('0001');
    }

    public function testFindProductsReturnsEmptyListWhenUnauthorized(): void
    {
        $count = self::getProductsCount();
        self::assertGreaterThan(0, $count);

        self::setAnonymousUser();

        $productsList = self::getProductService()->findProducts(new ProductQuery());

        self::assertEquals(0, $productsList->getTotalCount());
    }

    public function testUpdateProductThrowsUnauthorizedException(): void
    {
        $productService = self::getLocalProductService();
        $product = $productService->getProduct('0001');

        $updateStruct = $productService->newProductUpdateStruct($product);

        self::assertInstanceOf(
            ProductUpdateStruct::class,
            $updateStruct,
            'Local implementation of ProductService returns a specific, concrete ' . ProductUpdateStruct::class
        );

        $updateStruct->setField('name', 'Different Name');
        $updateStruct->setCode('different-code');

        self::setAnonymousUser();

        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessageMatches('/\'edit\' \'product\'/');

        $productService->updateProduct($updateStruct);
    }

    public function testDeleteProductThrowsUnauthorizedException(): void
    {
        $productService = self::getLocalProductService();
        $product = $productService->getProduct('0001');

        self::setAnonymousUser();

        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessageMatches('/\'delete\' \'product\'/');

        $productService->deleteProduct($product);
    }
}
