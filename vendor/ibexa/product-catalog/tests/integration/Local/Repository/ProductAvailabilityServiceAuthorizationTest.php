<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Local\Repository;

use Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException;
use Ibexa\Contracts\ProductCatalog\Values\Availability\ProductAvailabilityCreateStruct;
use Ibexa\Contracts\ProductCatalog\Values\Availability\ProductAvailabilityUpdateStruct;

/**
 * @covers \Ibexa\ProductCatalog\Local\Repository\ProductAvailabilityService
 *
 * @group product-availability-service
 */
final class ProductAvailabilityServiceAuthorizationTest extends BaseProductAvailabilityServiceTest
{
    private const PRODUCT_CODE = '0001';
    private const PRODUCT_CODE_WITH_AVAILABILITY = '0002';

    public function testCreateProductAvailabilityThrowsUnauthorizedException(): void
    {
        $product = $this->getProductServiceInstance()->getProduct(self::PRODUCT_CODE);

        self::setAnonymousUser();

        $createStruct = new ProductAvailabilityCreateStruct(
            $product,
            true,
            false,
            12
        );

        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessageMatches('/\'edit\' \'product\'/');

        self::getProductAvailabilityService()->createProductAvailability($createStruct);
    }

    public function testUpdateProductAvailability(): void
    {
        $product = $this->getProductServiceInstance()->getProduct(self::PRODUCT_CODE_WITH_AVAILABILITY);

        self::setAnonymousUser();

        $updateStruct = new ProductAvailabilityUpdateStruct(
            $product,
            true,
            false,
            12
        );

        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessageMatches('/\'edit\' \'product\'/');

        self::getProductAvailabilityService()->updateProductAvailability($updateStruct);
    }

    public function testGetProductAvailability(): void
    {
        $product = $this->getProductServiceInstance()->getProduct(self::PRODUCT_CODE_WITH_AVAILABILITY);

        self::setAnonymousUser();

        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessageMatches('/\'view\' \'product\'/');

        self::getProductAvailabilityService()->getAvailability($product);
    }

    public function testHasProductAvailability(): void
    {
        $this->getProductServiceInstance()->getProduct(self::PRODUCT_CODE);

        self::setAnonymousUser();

        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessageMatches('/\'view\' \'product\'/');

        $this->getProductServiceInstance()->getProduct(self::PRODUCT_CODE_WITH_AVAILABILITY);
    }

    public function testIncreaseProductAvailability(): void
    {
        $product = $this->getProductServiceInstance()->getProduct(self::PRODUCT_CODE_WITH_AVAILABILITY);

        self::setAnonymousUser();

        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessageMatches('/\'edit\' \'product\'/');

        self::getProductAvailabilityService()->increaseProductAvailability(
            $product,
            42
        );
    }

    public function testDecreaseProductAvailability(): void
    {
        $product = $this->getProductServiceInstance()->getProduct(self::PRODUCT_CODE_WITH_AVAILABILITY);

        self::setAnonymousUser();

        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessageMatches('/\'edit\' \'product\'/');

        self::getProductAvailabilityService()->decreaseProductAvailability(
            $product,
            6
        );
    }

    public function testDeleteProductAvailability(): void
    {
        $product = $this->getProductServiceInstance()->getProduct(self::PRODUCT_CODE_WITH_AVAILABILITY);

        self::setAnonymousUser();

        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessageMatches('/\'delete\' \'product\'/');

        self::getProductAvailabilityService()->deleteProductAvailability(
            $product
        );
    }
}
