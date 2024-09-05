<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Local\Repository;

use Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException;
use Ibexa\Contracts\ProductCatalog\Local\Values\ProductType\ProductTypeCreateStruct;
use Ibexa\Contracts\ProductCatalog\Local\Values\ProductType\ProductTypeUpdateStruct;
use Ibexa\Contracts\ProductCatalog\Values\ProductType\ProductTypeQuery;

/**
 * @covers \Ibexa\ProductCatalog\Local\Repository\ProductTypeService
 *
 * @group product-type-service
 */
final class ProductTypeServiceAuthorizationTest extends BaseProductTypeServiceTest
{
    public function testAddProductTypeThrowsUnauthorizedException(): void
    {
        self::setAnonymousUser();

        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessageMatches('/\'create\' \'product_type\'/');

        self::getLocalProductTypeService()->createProductType(
            $this->createMock(ProductTypeCreateStruct::class)
        );
    }

    public function testUpdateProductTypeThrowsUnauthorizedException(): void
    {
        self::setAnonymousUser();

        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessageMatches('/\'edit\' \'product_type\'/');

        self::getLocalProductTypeService()->updateProductType(
            $this->createMock(ProductTypeUpdateStruct::class)
        );
    }

    public function testGetProductTypeThrowsUnauthorizedException(): void
    {
        self::setAnonymousUser();

        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessageMatches('/\'view\' \'product_type\'/');

        self::getProductTypeService()->getProductType('dress');
    }

    public function testFindProductTypesThrowsUnauthorizedException(): void
    {
        self::setAnonymousUser();

        $productTypesList = self::getProductTypeService()->findProductTypes(new ProductTypeQuery());

        self::assertEquals(0, $productTypesList->getTotalCount());
    }

    public function testDeleteProductTypeThrowsUnauthorizedException(): void
    {
        $productTypeService = self::getLocalProductTypeService();

        $productType = self::getProductTypeService()->getProductType('dress');

        self::setAnonymousUser();

        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessageMatches('/\'delete\' \'product_type\'/');

        $productTypeService->deleteProductType($productType);
    }
}
