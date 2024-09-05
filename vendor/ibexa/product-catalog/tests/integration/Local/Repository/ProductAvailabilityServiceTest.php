<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Local\Repository;

use Ibexa\Contracts\ProductCatalog\Local\Values\Product\ProductVariantCreateStruct;
use Ibexa\Contracts\ProductCatalog\Values\Availability\AvailabilityContextInterface;
use Ibexa\Contracts\ProductCatalog\Values\Availability\ProductAvailabilityCreateStruct;
use Ibexa\Contracts\ProductCatalog\Values\Availability\ProductAvailabilityUpdateStruct;
use Ibexa\Contracts\ProductCatalog\Values\AvailabilityAwareInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;
use Ibexa\ProductCatalog\Local\Repository\Values\AvailabilityContext\AvailabilityContext;

/**
 * @covers \Ibexa\ProductCatalog\Local\Repository\ProductAvailabilityService
 *
 * @group product-availability-service
 */
final class ProductAvailabilityServiceTest extends BaseProductAvailabilityServiceTest
{
    private const PRODUCT_CODE = '0001';
    private const PRODUCT_CODE_WITH_AVAILABILITY = '0002';
    private const PRODUCT_CODE_WITH_VARIANTS = 'TROUSERS_0001';
    private const PRODUCT_VARIANT_WITH_AVAILABILITY_CODE_1 = 'TROUSERS_SMALL';
    private const PRODUCT_VARIANT_WITH_AVAILABILITY_CODE_2 = 'TROUSERS_MEDIUM';

    /**
     * @dataProvider dataProviderForCreateProductAvailability
     */
    public function testCreateProductAvailability(
        bool $availability,
        bool $isInfinite,
        ?int $stock
    ): void {
        $product = $this->getProductServiceInstance()->getProduct(self::PRODUCT_CODE);

        $createStruct = new ProductAvailabilityCreateStruct(
            $product,
            $availability,
            $isInfinite,
            $stock
        );

        $productAvailability = self::getProductAvailabilityService()->createProductAvailability($createStruct);

        self::assertSame($stock, $productAvailability->getStock());
        self::assertSame($availability, $productAvailability->isAvailable());
    }

    /**
     * @dataProvider dataProviderForCreateProductAvailability
     */
    public function testCreateProductVariantAvailability(
        bool $availability,
        bool $isInfinite,
        ?int $stock
    ): void {
        $productVariant1 = $this->createProductVariantWithAvailability(
            self::PRODUCT_VARIANT_WITH_AVAILABILITY_CODE_1,
            $availability,
            $isInfinite,
            $stock
        );

        $this->createProductVariantWithAvailability(
            self::PRODUCT_VARIANT_WITH_AVAILABILITY_CODE_2,
            $availability,
            $isInfinite,
            $stock
        );

        /** @var \Ibexa\Contracts\ProductCatalog\Values\ProductVariantInterface $productVariant1 */
        $baseProduct = $productVariant1->getBaseProduct();
        /** @var \Ibexa\Contracts\ProductCatalog\Values\AvailabilityAwareInterface $baseProduct */
        $baseProductAvailability = $baseProduct->getAvailability();

        self::assertSame($stock === null ? null : $stock * 2, $baseProductAvailability->getStock());
        self::assertSame($availability, $baseProductAvailability->isAvailable());
        self::assertSame($isInfinite, $baseProductAvailability->isInfinite());
    }

    /**
     * @return iterable<string, array{bool,bool,int|null}>
     */
    public function dataProviderForCreateProductAvailability(): iterable
    {
        yield 'Available, Stock set (12)' => [
            true,
            false,
            12,
        ];

        yield 'Available, Stock infinite (null)' => [
            true,
            true,
            null,
        ];

        yield 'Unavailable, Stock set (12)' => [
            false,
            false,
            12,
        ];

        yield 'Unavailable, Stock infinite (null)' => [
            false,
            true,
            null,
        ];
    }

    /**
     * @dataProvider dataProviderForUpdateProductAvailability
     */
    public function testUpdateProductAvailability(
        ?bool $availability,
        ?bool $isInfinite,
        ?int $stock,
        bool $expectedAvailability,
        bool $expectedIsInfinite,
        ?int $expectedStock
    ): void {
        $product = $this->getProductServiceInstance()->getProduct(self::PRODUCT_CODE_WITH_AVAILABILITY);

        $updateStruct = new ProductAvailabilityUpdateStruct(
            $product,
            $availability,
            $isInfinite,
            $stock
        );

        $productAvailability = self::getProductAvailabilityService()->updateProductAvailability($updateStruct);

        self::assertSame($expectedAvailability, $productAvailability->isAvailable());
        self::assertSame($expectedIsInfinite, $productAvailability->isInfinite());
        self::assertSame($expectedStock, $productAvailability->getStock());
    }

    /**
     * @return iterable<string, array{bool|null, bool|null, int|null, bool, bool, int|null}>
     */
    public function dataProviderForUpdateProductAvailability(): iterable
    {
        yield 'Available, Stock set (12), product should be available' => [
            true,
            false,
            12,
            true,
            false,
            12,
        ];

        yield 'Available, Stock infinite (null), product should be available' => [
            true,
            true,
            null,
            true,
            true,
            null,
        ];

        yield 'Unavailable, Stock set (12), product should be unavailable' => [
            false,
            false,
            12,
            false,
            false,
            12,
        ];

        yield 'Unavailable, Stock infinite (null), product should be unavailable' => [
            false,
            true,
            null,
            false,
            true,
            null,
        ];

        yield 'Availability does not change, Stock set (12), product should be still available' => [
            null,
            false,
            12,
            true,
            false,
            12,
        ];

        yield 'Availability does not change, Stock infinite (null), product should be still available' => [
            null,
            null,
            null,
            true,
            false,
            10,
        ];
    }

    /**
     * @dataProvider dataProviderForUpdateProductVariantAvailability
     */
    public function testUpdateProductVariantAvailability(
        ?bool $availability,
        ?bool $isInfinite,
        ?int $stock,
        bool $expectedAvailability,
        bool $expectedIsInfinite,
        ?int $expectedStock,
        bool $expectedBaseProductAvailability,
        bool $expectedBaseProductIsInfinite,
        ?int $expectedBaseProductStock
    ): void {
        $initialStock = 10;

        $productVariant = $this->createProductVariantWithAvailability(
            self::PRODUCT_VARIANT_WITH_AVAILABILITY_CODE_1,
            $availability,
            $isInfinite,
            $stock
        );

        $this->createProductVariantWithAvailability(
            self::PRODUCT_VARIANT_WITH_AVAILABILITY_CODE_2,
            true,
            false,
            $initialStock
        );

        $updateStruct = new ProductAvailabilityUpdateStruct(
            $productVariant,
            $availability,
            $isInfinite,
            $stock
        );

        $productVariantAvailability = self::getProductAvailabilityService()->updateProductAvailability($updateStruct);

        self::assertSame($expectedAvailability, $productVariantAvailability->isAvailable());
        self::assertSame($expectedIsInfinite, $productVariantAvailability->isInfinite());
        self::assertSame($expectedStock, $productVariantAvailability->getStock());

        /** @var \Ibexa\Contracts\ProductCatalog\Values\ProductVariantInterface $productVariant */
        $baseProduct = $productVariant->getBaseProduct();
        /** @var \Ibexa\Contracts\ProductCatalog\Values\AvailabilityAwareInterface $baseProduct */
        $baseProductAvailability = $baseProduct->getAvailability();

        self::assertSame($expectedBaseProductAvailability, $baseProductAvailability->isAvailable());
        self::assertSame($expectedBaseProductIsInfinite, $baseProductAvailability->isInfinite());
        self::assertSame($expectedBaseProductStock, $baseProductAvailability->getStock());
    }

    /**
     * @return iterable<string, array{bool|null, bool|null, int|null, bool, bool, int|null, bool, bool, int|null}>
     */
    public function dataProviderForUpdateProductVariantAvailability(): iterable
    {
        yield 'Available, Stock set (12), base product should be available' => [
            true,
            false,
            12,
            true,
            false,
            12,
            true,
            false,
            22,
        ];

        yield 'Available, Stock infinite (null), base product should be available' => [
            true,
            true,
            null,
            true,
            true,
            null,
            true,
            true,
            null,
        ];

        yield 'Unavailable, Stock set (12), base product should be available' => [
            false,
            false,
            12,
            false,
            false,
            12,
            true,
            false,
            22,
        ];

        yield 'Unavailable, Stock infinite (null), base product should be available' => [
            false,
            true,
            null,
            false,
            true,
            null,
            true,
            true,
            null,
        ];
    }

    public function testGetProductAvailability(): void
    {
        $product = $this->getProductServiceInstance()->getProduct(self::PRODUCT_CODE_WITH_AVAILABILITY);

        $productAvailability = self::getProductAvailabilityService()->getAvailability($product);

        self::assertSame(10, $productAvailability->getStock());
        self::assertTrue($productAvailability->isAvailable());

        $productAvailability = self::getProductAvailabilityService()->getAvailability(
            $product,
            new AvailabilityContext()
        );

        self::assertSame(10, $productAvailability->getStock());
        self::assertTrue($productAvailability->isAvailable());

        $productAvailability = self::getProductAvailabilityService()->getAvailability(
            $product,
            new AvailabilityContext(15)
        );

        self::assertSame(10, $productAvailability->getStock());
        self::assertFalse($productAvailability->isAvailable());
    }

    public function testGetProductVariantAvailability(): void
    {
        $availabilityService = self::getProductAvailabilityService();

        $productVariant = $this->createProductVariantWithAvailability(
            self::PRODUCT_VARIANT_WITH_AVAILABILITY_CODE_1,
            true,
            false,
            10
        );

        $productVariantAvailability = $availabilityService->getAvailability($productVariant);

        self::assertSame(10, $productVariantAvailability->getStock());
        self::assertTrue($productVariantAvailability->isAvailable());

        /** @var \Ibexa\Contracts\ProductCatalog\Values\ProductVariantInterface $productVariant */
        $baseProduct = $productVariant->getBaseProduct();
        /** @var \Ibexa\Contracts\ProductCatalog\Values\AvailabilityAwareInterface $baseProduct */
        $baseProductAvailability = $baseProduct->getAvailability();

        self::assertSame(10, $baseProductAvailability->getStock());
        self::assertTrue($baseProductAvailability->isAvailable());

        $productVariantAvailability = $availabilityService->getAvailability(
            $productVariant,
            new AvailabilityContext()
        );

        self::assertSame(10, $productVariantAvailability->getStock());
        self::assertTrue($productVariantAvailability->isAvailable());
        self::assertSame(10, $baseProductAvailability->getStock());
        self::assertTrue($baseProductAvailability->isAvailable());

        $productVariantAvailability = $availabilityService->getAvailability(
            $productVariant,
            new AvailabilityContext(15)
        );

        self::assertSame(10, $productVariantAvailability->getStock());
        self::assertFalse($productVariantAvailability->isAvailable());
        self::assertSame(10, $baseProductAvailability->getStock());
    }

    /**
     * @dataProvider dataProviderForTestAvailabilityAwareProductIsAvailable
     */
    public function testAvailabilityAwareProductIsAvailable(
        string $code,
        ?AvailabilityContextInterface $context,
        bool $expectedResult
    ): void {
        $product = $this->getProductServiceInstance()->getProduct($code);

        if (!($product instanceof AvailabilityAwareInterface)) {
            self::markTestSkipped('Product needs to implement AvailabilityAwareInterface');
        }

        self::assertEquals($expectedResult, $product->isAvailable($context));
    }

    /**
     * @return iterable<string,array{string,?AvailabilityContextInterface,bool}>
     */
    public function dataProviderForTestAvailabilityAwareProductIsAvailable(): iterable
    {
        yield 'available' => [
            self::PRODUCT_CODE_WITH_AVAILABILITY,
            null,
            true,
        ];

        yield 'available in requested amount' => [
            self::PRODUCT_CODE_WITH_AVAILABILITY,
            new AvailabilityContext(5),
            true,
        ];

        yield 'non available in requested amount' => [
            self::PRODUCT_CODE_WITH_AVAILABILITY,
            new AvailabilityContext(15),
            false,
        ];

        yield 'undefined availability' => [
            self::PRODUCT_CODE,
            null,
            false,
        ];
    }

    /**
     * @dataProvider dataProviderForTestAvailabilityAwareProductVariantIsAvailable
     */
    public function testAvailabilityAwareProductVariantIsAvailable(
        string $code,
        ?AvailabilityContextInterface $context,
        bool $expectedResult
    ): void {
        /** @var \Ibexa\Contracts\ProductCatalog\Values\AvailabilityAwareInterface $productVariant */
        $productVariant = $this->createProductVariantWithAvailability(
            $code,
            true,
            false,
            10
        );

        self::assertEquals($expectedResult, $productVariant->isAvailable($context));
    }

    /**
     * @return iterable<string,array{string,?AvailabilityContextInterface,bool}>
     */
    public function dataProviderForTestAvailabilityAwareProductVariantIsAvailable(): iterable
    {
        yield 'available' => [
            self::PRODUCT_VARIANT_WITH_AVAILABILITY_CODE_1,
            null,
            true,
        ];

        yield 'available in requested amount' => [
            self::PRODUCT_VARIANT_WITH_AVAILABILITY_CODE_1,
            new AvailabilityContext(5),
            true,
        ];

        yield 'non available in requested amount' => [
            self::PRODUCT_VARIANT_WITH_AVAILABILITY_CODE_1,
            new AvailabilityContext(15),
            false,
        ];
    }

    public function testHasProductAvailability(): void
    {
        $product = $this->getProductServiceInstance()->getProduct(self::PRODUCT_CODE);
        $productWithAvailability = $this->getProductServiceInstance()->getProduct(self::PRODUCT_CODE_WITH_AVAILABILITY);

        self::assertFalse(self::getProductAvailabilityService()->hasAvailability($product));
        self::assertTrue(self::getProductAvailabilityService()->hasAvailability($productWithAvailability));
    }

    public function testHasProductVariantAvailability(): void
    {
        $availabilityService = self::getProductAvailabilityService();
        $productVariant = $this->createProductVariant(self::PRODUCT_VARIANT_WITH_AVAILABILITY_CODE_1);

        $productVariantWithAvailability = $this->createPredefinedProductVariant();

        self::assertFalse($availabilityService->hasAvailability($productVariant));
        self::assertTrue($availabilityService->hasAvailability($productVariantWithAvailability));

        /** @var \Ibexa\Contracts\ProductCatalog\Values\ProductVariantInterface $productVariantWithAvailability */
        $baseProduct = $productVariantWithAvailability->getBaseProduct();

        self::assertTrue($availabilityService->hasAvailability($baseProduct));
    }

    public function testChangingProductAvailabilityWithoutChangingStock(): void
    {
        $product = $this->getProductServiceInstance()->getProduct(self::PRODUCT_CODE_WITH_AVAILABILITY);
        $availability = self::getProductAvailabilityService()->getAvailability($product);

        self::assertTrue($availability->isAvailable());
        $stock = $availability->getStock();
        self::assertIsInt($stock);

        $struct = new ProductAvailabilityUpdateStruct(
            $product,
            false,
        );

        self::getProductAvailabilityService()->updateProductAvailability($struct);

        $availability = self::getProductAvailabilityService()->getAvailability($product);

        self::assertFalse($availability->isAvailable());
        self::assertSame($stock, $availability->getStock());
    }

    public function testChangingProductVariantAvailabilityWithoutChangingStock(): void
    {
        $availabilityService = self::getProductAvailabilityService();
        $productVariant = $this->createPredefinedProductVariant();
        $availability = $availabilityService->getAvailability($productVariant);

        self::assertTrue($availability->isAvailable());
        $stock = $availability->getStock();
        self::assertIsInt($stock);

        $struct = new ProductAvailabilityUpdateStruct($productVariant, false);
        $availabilityService->updateProductAvailability($struct);
        $availability = $availabilityService->getAvailability($productVariant);

        self::assertFalse($availability->isAvailable());
        self::assertSame($stock, $availability->getStock());

        /** @var \Ibexa\Contracts\ProductCatalog\Values\ProductVariantInterface $productVariant */
        $baseProduct = $productVariant->getBaseProduct();
        /** @var \Ibexa\Contracts\ProductCatalog\Values\AvailabilityAwareInterface $baseProduct */
        $baseProductAvailability = $baseProduct->getAvailability();

        self::assertFalse($baseProductAvailability->isAvailable());
        self::assertSame($stock, $baseProductAvailability->getStock());
    }

    public function testIncreaseProductAvailability(): void
    {
        $product = $this->getProductServiceInstance()->getProduct(self::PRODUCT_CODE_WITH_AVAILABILITY);

        $productAvailability = self::getProductAvailabilityService()->increaseProductAvailability(
            $product,
            42
        );

        self::assertSame(52, $productAvailability->getStock());
    }

    public function testIncreaseProductVariantAvailability(): void
    {
        $productVariant = $this->createPredefinedProductVariant();

        $productVariantAvailability = self::getProductAvailabilityService()->increaseProductAvailability(
            $productVariant,
            42
        );

        self::assertSame(52, $productVariantAvailability->getStock());

        /** @var \Ibexa\Contracts\ProductCatalog\Values\ProductVariantInterface $productVariant */
        $baseProduct = $productVariant->getBaseProduct();
        /** @var \Ibexa\Contracts\ProductCatalog\Values\AvailabilityAwareInterface $baseProduct */
        $baseProductAvailability = $baseProduct->getAvailability();

        self::assertSame(52, $baseProductAvailability->getStock());
    }

    public function testIncreaseProductAvailabilityWithInfiniteStock(): void
    {
        $product = $this->getProductServiceInstance()->getProduct(self::PRODUCT_CODE_WITH_AVAILABILITY);

        $this->setInfiniteStock($product);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Infinite stock cannot be increased'
        );

        self::getProductAvailabilityService()->increaseProductAvailability(
            $product,
            42
        );
    }

    public function testIncreaseProductVariantAvailabilityWithInfiniteStock(): void
    {
        $productVariant = $this->createPredefinedProductVariant();

        $this->setInfiniteStock($productVariant);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Infinite stock cannot be increased'
        );

        self::getProductAvailabilityService()->increaseProductAvailability(
            $productVariant,
            10
        );
    }

    public function testDecreaseProductAvailability(): void
    {
        $product = $this->getProductServiceInstance()->getProduct(self::PRODUCT_CODE_WITH_AVAILABILITY);

        $productAvailability = self::getProductAvailabilityService()->decreaseProductAvailability(
            $product,
            6
        );

        self::assertSame(4, $productAvailability->getStock());
    }

    public function testDecreaseProductVariantAvailability(): void
    {
        $productVariant = $this->createPredefinedProductVariant();

        $productAvailability = self::getProductAvailabilityService()->decreaseProductAvailability(
            $productVariant,
            6
        );

        self::assertSame(4, $productAvailability->getStock());

        /** @var \Ibexa\Contracts\ProductCatalog\Values\ProductVariantInterface $productVariant */
        $baseProduct = $productVariant->getBaseProduct();
        /** @var \Ibexa\Contracts\ProductCatalog\Values\AvailabilityAwareInterface $baseProduct */
        $baseProductAvailability = $baseProduct->getAvailability();

        self::assertSame(4, $baseProductAvailability->getStock());
    }

    public function testDecreaseProductAvailabilityByTooMany(): void
    {
        $product = $this->getProductServiceInstance()->getProduct(self::PRODUCT_CODE_WITH_AVAILABILITY);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            "Argument 'amount' is invalid: The stock cannot be reduced to less than zero. Stock may have changed before your request. Please check the current stock in the database."
        );

        self::getProductAvailabilityService()->decreaseProductAvailability(
            $product,
            42
        );
    }

    public function testDecreaseProductVariantAvailabilityByTooMany(): void
    {
        $productVariant = $this->createPredefinedProductVariant();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            "Argument 'amount' is invalid: The stock cannot be reduced to less than zero. Stock may have changed before your request. Please check the current stock in the database."
        );

        self::getProductAvailabilityService()->decreaseProductAvailability(
            $productVariant,
            15
        );
    }

    public function testDecreaseProductAvailabilityWithInfiniteStock(): void
    {
        $product = $this->getProductServiceInstance()->getProduct(self::PRODUCT_CODE_WITH_AVAILABILITY);

        $this->setInfiniteStock($product);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Infinite stock cannot be decreased'
        );

        self::getProductAvailabilityService()->decreaseProductAvailability(
            $product,
            42
        );
    }

    public function testDecreaseProductVariantAvailabilityWithInfiniteStock(): void
    {
        $productVariant = $this->createPredefinedProductVariant();

        $this->setInfiniteStock($productVariant);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Infinite stock cannot be decreased'
        );

        self::getProductAvailabilityService()->decreaseProductAvailability(
            $productVariant,
            5
        );
    }

    public function testDeleteProductAvailability(): void
    {
        $product = $this->getProductServiceInstance()->getProduct(self::PRODUCT_CODE);

        self::getProductAvailabilityService()->deleteProductAvailability(
            $product,
        );

        self::assertFalse(self::getProductAvailabilityService()->hasAvailability($product));
    }

    public function testDeleteBaseProductAvailability(): void
    {
        $productVariant1 = $this->createProductVariantWithAvailability(
            self::PRODUCT_VARIANT_WITH_AVAILABILITY_CODE_1,
            true,
            true,
            null
        );
        $productVariant2 = $this->createProductVariantWithAvailability(
            self::PRODUCT_VARIANT_WITH_AVAILABILITY_CODE_2,
            true,
            false,
            10
        );

        $baseProduct = $this
            ->getProductServiceInstance()
            ->getProduct(self::PRODUCT_CODE_WITH_VARIANTS);

        $productAvailabilityService = self::getProductAvailabilityService();
        $productAvailabilityService->deleteProductAvailability($baseProduct);

        self::assertFalse($productAvailabilityService->hasAvailability($baseProduct));
        self::assertFalse($productAvailabilityService->hasAvailability($productVariant1));
        self::assertFalse($productAvailabilityService->hasAvailability($productVariant2));
    }

    public function testDeleteProductVariantAvailability(): void
    {
        $availabilityService = self::getProductAvailabilityService();
        $productVariant = $this->createPredefinedProductVariant();

        $availabilityService->deleteProductAvailability($productVariant);

        self::assertFalse($availabilityService->hasAvailability($productVariant));

        /** @var \Ibexa\Contracts\ProductCatalog\Values\ProductVariantInterface $productVariant */
        $baseProduct = $productVariant->getBaseProduct();

        self::assertFalse($availabilityService->hasAvailability($baseProduct));
    }

    private function createProductVariantWithAvailability(
        string $productVariantCode,
        ?bool $availability,
        ?bool $isInfinite,
        ?int $stock
    ): ProductInterface {
        $productVariant = $this->createProductVariant($productVariantCode);
        $availabilityService = self::getProductAvailabilityService();

        $availabilityService->createProductAvailability(
            new ProductAvailabilityCreateStruct(
                $productVariant,
                $availability ?? false,
                $isInfinite ?? false,
                $stock
            )
        );

        return $productVariant;
    }

    private function createPredefinedProductVariant(): ProductInterface
    {
        return $this->createProductVariantWithAvailability(
            self::PRODUCT_VARIANT_WITH_AVAILABILITY_CODE_2,
            true,
            false,
            10
        );
    }

    private function createProductVariant(string $productVariantCode): ProductInterface
    {
        $productService = $this->getProductServiceInstance();
        $attributes = [
            'bar' => true,
            'baz' => 10,
        ];

        $productService->createProductVariants(
            $productService->getProduct(self::PRODUCT_CODE_WITH_VARIANTS),
            [new ProductVariantCreateStruct($attributes, $productVariantCode)]
        );

        return $productService->getProduct($productVariantCode);
    }

    private function setInfiniteStock(ProductInterface $product): void
    {
        $updateStruct = new ProductAvailabilityUpdateStruct(
            $product,
            true,
            true,
            null
        );

        self::getProductAvailabilityService()->updateProductAvailability($updateStruct);
    }
}
