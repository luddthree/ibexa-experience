<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Local\Repository;

final class ProductCodeChangeTest extends BaseProductServiceTest
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->executeMigration('product_code_change_setup.yaml');
    }

    protected function tearDown(): void
    {
        $this->executeMigration('product_code_change_teardown.yaml');
    }

    public function testUpdateProductCodeDoesNotRemovePrice(): void
    {
        $productService = self::getLocalProductService();
        $priceService = self::getProductPriceService();
        $currencyService = self::getCurrencyService();
        $product = $productService->getProduct('CODE_CHANGE_PRICE_TEST');

        $currency = $currencyService->getCurrencyByCode('USD');

        self::assertEquals(200, $priceService->getPriceByProductAndCurrency($product, $currency)->getAmount());

        $updateStruct = $productService->newProductUpdateStruct($product);
        $updateStruct->setCode('no-change-to-price-code');

        $productWithNewCode = $productService->updateProduct($updateStruct);

        self::assertEquals(200, $priceService->getPriceByProductAndCurrency($productWithNewCode, $currency)->getAmount());
    }

    public function testUpdateProductCodeDoesNotRemoveAvailability(): void
    {
        $productService = self::getLocalProductService();
        $product = $productService->getProduct('CODE_CHANGE_AVAILABILITY_TEST');

        $availabilityService = self::getProductAvailabilityService();

        $availability = $availabilityService->getAvailability($product);

        $updateStruct = $productService->newProductUpdateStruct($product);
        $updateStruct->setCode('no-change-to-availability-code');

        $productWithNewCode = $productService->updateProduct($updateStruct);

        self::assertEquals($availability->getStock(), $availabilityService->getAvailability($productWithNewCode)->getStock());
    }
}
