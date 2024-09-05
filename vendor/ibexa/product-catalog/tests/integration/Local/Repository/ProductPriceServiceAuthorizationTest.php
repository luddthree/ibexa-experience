<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Local\Repository;

use Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException;
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\ProductCatalog\Values\CurrencyInterface;
use Ibexa\Contracts\ProductCatalog\Values\Price\Create\Struct\ProductPriceCreateStruct;
use Ibexa\Contracts\ProductCatalog\Values\Price\Delete\Struct\ProductPriceDeleteStruct;
use Ibexa\Contracts\ProductCatalog\Values\Price\Update\Struct\ProductPriceUpdateStruct;
use Money\Currency;
use Money\Money;

/**
 * @covers \Ibexa\ProductCatalog\Local\Repository\ProductPriceService
 *
 * @group product-price-service
 */
final class ProductPriceServiceAuthorizationTest extends BaseProductPriceServiceTest
{
    public function testFindPricesByProductCodeThrowsUnauthorizedException(): void
    {
        self::setAnonymousUser();

        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessageMatches('/\'view\' \'product\'/');

        $this->productPriceService->findPricesByProductCode(self::EXAMPLE_PRODUCT_CODE);
    }

    public function testUpdatePriceForProductWithoutAuthorizationThrowsUnauthorizedException(): void
    {
        $price = $this->productPriceService->getPriceById(5);

        $struct = new ProductPriceUpdateStruct(
            $price,
            new Money('6600', new Currency('EUR')),
        );

        self::setAnonymousUser();

        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessageMatches('/\'edit\' \'product\'/');

        $this->productPriceService->updateProductPrice($struct);
    }

    public function testCreateProductPrice(): void
    {
        $product = self::getLocalProductService()->getProduct(self::EXAMPLE_PRODUCT_CODE2);
        $currency = $this->createMock(CurrencyInterface::class);
        /** @var \Ibexa\ProductCatalog\Local\Repository\Values\Product $product */
        $struct = new ProductPriceCreateStruct(
            $product,
            $currency,
            new Money('6600', new Currency('EUR')),
            null,
            null
        );

        self::setAnonymousUser();

        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessageMatches('/\'edit\' \'product\'/');

        $this->productPriceService->createProductPrice($struct);
    }

    public function testFindPriceById(): void
    {
        self::setAnonymousUser();

        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessageMatches('/\'view\' \'product\'/');

        $this->productPriceService->getPriceById(5);
    }

    public function testDeletePriceThrowsUnauthorizedException(): void
    {
        $price = $this->productPriceService->getPriceById(5);

        $user = $this->createUserWithPolicies(
            'user',
            [
                ['module' => 'product', 'function' => 'view'],
            ]
        );
        $repository = self::getServiceByClassName(Repository::class);
        $permissionResolver = $repository->getPermissionResolver();
        $permissionResolver->setCurrentUserReference($user);

        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessageMatches('/\'delete\' \'product\'/');

        $this->productPriceService->deletePrice(
            new ProductPriceDeleteStruct($price)
        );
    }
}
