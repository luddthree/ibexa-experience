<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Local\Event;

use Ibexa\Contracts\ProductCatalog\Local\Events\BeforeCreateProductEvent;
use Ibexa\Contracts\ProductCatalog\Local\Events\BeforeCreateProductVariantsEvent;
use Ibexa\Contracts\ProductCatalog\Local\Events\BeforeDeleteBaseProductVariantsEvent;
use Ibexa\Contracts\ProductCatalog\Local\Events\BeforeDeleteProductEvent;
use Ibexa\Contracts\ProductCatalog\Local\Events\BeforeUpdateProductEvent;
use Ibexa\Contracts\ProductCatalog\Local\Events\BeforeUpdateProductVariantEvent;
use Ibexa\Contracts\ProductCatalog\Local\Events\CreateProductEvent;
use Ibexa\Contracts\ProductCatalog\Local\Events\CreateProductVariantsEvent;
use Ibexa\Contracts\ProductCatalog\Local\Events\DeleteBaseProductVariantsEvent;
use Ibexa\Contracts\ProductCatalog\Local\Events\DeleteProductEvent;
use Ibexa\Contracts\ProductCatalog\Local\Events\UpdateProductEvent;
use Ibexa\Contracts\ProductCatalog\Local\Events\UpdateProductVariantEvent;
use Ibexa\Contracts\ProductCatalog\Local\LocalProductServiceInterface;
use Ibexa\Contracts\ProductCatalog\Local\Values\Product\ProductCreateStruct;
use Ibexa\Contracts\ProductCatalog\Local\Values\Product\ProductUpdateStruct;
use Ibexa\Contracts\ProductCatalog\Local\Values\Product\ProductVariantCreateStruct;
use Ibexa\Contracts\ProductCatalog\Local\Values\Product\ProductVariantUpdateStruct;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductVariantInterface;
use Ibexa\ProductCatalog\Local\Repository\Event\ProductService;
use PHPUnit\Framework\Constraint\Constraint;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * @template-extends \Ibexa\Tests\ProductCatalog\Local\Event\AbstractEventServiceTest<
 *    \Ibexa\Contracts\ProductCatalog\Local\LocalProductServiceInterface
 * >
 */
final class ProductServiceTest extends AbstractEventServiceTest
{
    private ProductService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new ProductService($this->innerService, $this->eventDispatcher);
    }

    protected function getInnerServiceClass(): string
    {
        return LocalProductServiceInterface::class;
    }

    public function testCreateProductDispatchEvents(): void
    {
        $createStruct = $this->createMock(ProductCreateStruct::class);
        $expectedProduct = $this->createMock(ProductInterface::class);

        $this->assertInnerServiceIsCalled('createProduct', [$createStruct], $expectedProduct);
        $this->assertBeforeAndAfterEventsAreDispatched(
            $this->isValidBeforeCreateProductEvent($createStruct),
            $this->isValidCreateProductEvent($createStruct, $expectedProduct)
        );

        $actualProduct = $this->service->createProduct($createStruct);

        self::assertSame($expectedProduct, $actualProduct);
    }

    public function testCreateProductWhenBeforeEventStoppedPropagation(): void
    {
        $createStruct = $this->createMock(ProductCreateStruct::class);
        $expectedProduct = $this->createMock(ProductInterface::class);

        $this->assertInnerServiceIsNotCalled('createProduct', [$createStruct]);
        $this->assertBeforeEventIsDispatchedAndStopsPropagation(
            $this->isValidBeforeCreateProductEvent($createStruct),
            $this->getResultCallbackForProductEvent($expectedProduct)
        );

        $actualProduct = $this->service->createProduct($createStruct);

        self::assertSame($expectedProduct, $actualProduct);
    }

    public function testCreateProductWhenBeforeEventSetsResult(): void
    {
        $createStruct = $this->createMock(ProductCreateStruct::class);
        $expectedProduct = $this->createMock(ProductInterface::class);

        $this->assertInnerServiceIsNotCalled('createProduct', [$createStruct]);
        $this->assertBeforeAndAfterEventsAreDispatchedWithOverwrittenResult(
            $this->isValidBeforeCreateProductEvent($createStruct),
            $this->isValidCreateProductEvent($createStruct, $expectedProduct),
            $this->getResultCallbackForProductEvent($expectedProduct)
        );

        $actualProduct = $this->service->createProduct($createStruct);

        self::assertSame($expectedProduct, $actualProduct);
    }

    public function testCreateProductVariantsDispatchEvents(): void
    {
        $product = $this->createMock(ProductInterface::class);
        $createStructs = [
            new ProductVariantCreateStruct(['attr' => 'foo']),
            new ProductVariantCreateStruct(['attr' => 'bar']),
            new ProductVariantCreateStruct(['attr' => 'baz']),
        ];

        $this->assertInnerServiceIsCalled('createProductVariants', [$product, $createStructs]);
        $this->assertBeforeAndAfterEventsAreDispatched(
            $this->isValidBeforeCreateProductVariantsEvent($product, $createStructs),
            $this->isValidCreateProductVariantsEvent($product, $createStructs)
        );

        $this->service->createProductVariants($product, $createStructs);
    }

    public function testCreateProductVariantsWhenBeforeEventStoppedPropagation(): void
    {
        $product = $this->createMock(ProductInterface::class);
        $createStructs = [
            new ProductVariantCreateStruct(['attr' => 'foo']),
            new ProductVariantCreateStruct(['attr' => 'bar']),
            new ProductVariantCreateStruct(['attr' => 'baz']),
        ];

        $this->assertInnerServiceIsNotCalled('createProductVariants', [$product, $createStructs]);
        $this->assertBeforeEventIsDispatchedAndStopsPropagation(
            self::isInstanceOf(BeforeCreateProductVariantsEvent::class)
        );

        $this->service->createProductVariants($product, $createStructs);
    }

    public function testDeleteProductDispatchEvents(): void
    {
        $product = $this->createMock(ProductInterface::class);

        $this->assertInnerServiceIsCalled('deleteProduct', [$product]);
        $this->assertBeforeAndAfterEventsAreDispatched(
            $this->isValidBeforeDeleteProductEvent($product),
            $this->isValidDeleteProductEvent($product)
        );

        $this->service->deleteProduct($product);
    }

    public function testDeleteProductWhenBeforeEventStoppedPropagation(): void
    {
        $product = $this->createMock(ProductInterface::class);

        $this->assertInnerServiceIsNotCalled('deleteProduct', [$product]);
        $this->assertBeforeEventIsDispatchedAndStopsPropagation(
            self::isInstanceOf(BeforeDeleteProductEvent::class)
        );

        $this->service->deleteProduct($product);
    }

    public function testDeleteProductVariantsByBaseProductDispatchEvents(): void
    {
        $baseProduct = $this->createMock(ProductInterface::class);

        $this->assertInnerServiceIsCalled('deleteProductVariantsByBaseProduct', [$baseProduct]);
        $this->assertBeforeAndAfterEventsAreDispatched(
            $this->isValidBeforeDeleteBaseProductVariantsEvent($baseProduct),
            $this->isValidDeleteBaseProductVariantsEvent($baseProduct)
        );

        $this->service->deleteProductVariantsByBaseProduct($baseProduct);
    }

    public function testDeleteProductVariantsByBaseProductWhenBeforeEventStoppedPropagation(): void
    {
        $baseProduct = $this->createMock(ProductInterface::class);

        $this->assertInnerServiceIsNotCalled('deleteProductVariantsByBaseProduct', [$baseProduct]);
        $this->assertBeforeEventIsDispatchedAndStopsPropagation(
            self::isInstanceOf(BeforeDeleteBaseProductVariantsEvent::class)
        );

        $this->service->deleteProductVariantsByBaseProduct($baseProduct);
    }

    public function testUpdateProductDispatchEvents(): void
    {
        $updateStruct = $this->createMock(ProductUpdateStruct::class);
        $expectedProduct = $this->createMock(ProductInterface::class);

        $this->assertInnerServiceIsCalled('updateProduct', [$updateStruct], $expectedProduct);
        $this->assertBeforeAndAfterEventsAreDispatched(
            $this->isValidBeforeUpdateProductEvent($updateStruct),
            $this->isValidUpdateProductEvent($expectedProduct, $updateStruct)
        );

        $actualProduct = $this->service->updateProduct($updateStruct);

        self::assertSame($expectedProduct, $actualProduct);
    }

    public function testUpdateProductWhenBeforeEventStoppedPropagation(): void
    {
        $updateStruct = $this->createMock(ProductUpdateStruct::class);
        $expectedProduct = $this->createMock(ProductInterface::class);

        $this->assertInnerServiceIsNotCalled('updateProduct', [$updateStruct]);
        $this->assertBeforeEventIsDispatchedAndStopsPropagation(
            self::isInstanceOf(BeforeUpdateProductEvent::class),
            $this->getResultCallbackForProductEvent($expectedProduct)
        );

        $actualProduct = $this->service->updateProduct($updateStruct);

        self::assertSame($expectedProduct, $actualProduct);
    }

    public function testUpdateProductWhenBeforeEventSetsResult(): void
    {
        $updateStruct = $this->createMock(ProductUpdateStruct::class);
        $expectedProduct = $this->createMock(ProductInterface::class);

        $this->assertInnerServiceIsNotCalled('updateProduct', [$updateStruct]);
        $this->assertBeforeAndAfterEventsAreDispatchedWithOverwrittenResult(
            $this->isValidBeforeUpdateProductEvent($updateStruct),
            $this->isValidUpdateProductEvent($expectedProduct, $updateStruct),
            $this->getResultCallbackForProductEvent($expectedProduct)
        );

        $actualProduct = $this->service->updateProduct($updateStruct);

        self::assertSame($expectedProduct, $actualProduct);
    }

    public function testUpdateProductVariantDispatchEvents(): void
    {
        $productVariant = $this->createMock(ProductVariantInterface::class);
        $updateStruct = new ProductVariantUpdateStruct();
        $expectedProductVariant = $this->createMock(ProductVariantInterface::class);

        $this->assertInnerServiceIsCalled('updateProductVariant', [$productVariant, $updateStruct], $expectedProductVariant);
        $this->assertBeforeAndAfterEventsAreDispatched(
            $this->isValidBeforeUpdateProductVariantEvent($productVariant, $updateStruct),
            $this->isValidUpdateProductVariantEvent($expectedProductVariant, $updateStruct)
        );

        $actualProductVariant = $this->service->updateProductVariant($productVariant, $updateStruct);

        self::assertSame($expectedProductVariant, $actualProductVariant);
    }

    public function testUpdateProductVariantWhenBeforeEventStoppedPropagation(): void
    {
        $productVariant = $this->createMock(ProductVariantInterface::class);
        $updateStruct = new ProductVariantUpdateStruct();
        $expectedProductVariant = $this->createMock(ProductVariantInterface::class);

        $this->assertInnerServiceIsNotCalled('updateProductVariant', [$productVariant, $updateStruct]);
        $this->assertBeforeEventIsDispatchedAndStopsPropagation(
            self::isInstanceOf(BeforeUpdateProductVariantEvent::class),
            $this->getResultCallbackForProductVariantEvent($expectedProductVariant)
        );

        $actualProductVariant = $this->service->updateProductVariant($productVariant, $updateStruct);

        self::assertSame($expectedProductVariant, $actualProductVariant);
    }

    public function testUpdateProductVariantWhenBeforeEventSetsResult(): void
    {
        $productVariant = $this->createMock(ProductVariantInterface::class);
        $updateStruct = new ProductVariantUpdateStruct();
        $expectedProductVariant = $this->createMock(ProductVariantInterface::class);

        $this->assertInnerServiceIsNotCalled('updateProductVariant', [$productVariant, $updateStruct]);
        $this->assertBeforeAndAfterEventsAreDispatchedWithOverwrittenResult(
            $this->isValidBeforeUpdateProductVariantEvent($productVariant, $updateStruct),
            $this->isValidUpdateProductVariantEvent($expectedProductVariant, $updateStruct),
            $this->getResultCallbackForProductVariantEvent($expectedProductVariant)
        );

        $actualProductVariant = $this->service->updateProductVariant($productVariant, $updateStruct);

        self::assertSame($expectedProductVariant, $actualProductVariant);
    }

    private function isValidBeforeCreateProductEvent(
        ProductCreateStruct $expectedCreateStruct
    ): Constraint {
        $callback = static fn (BeforeCreateProductEvent $event): bool => [
            $event->getCreateStruct(),
        ] === [$expectedCreateStruct];

        return $this->isValidEvent(BeforeCreateProductEvent::class, $callback);
    }

    private function isValidBeforeDeleteProductEvent(
        ProductInterface $expectedProduct
    ): Constraint {
        $callback = static fn (BeforeDeleteProductEvent $event): bool => [
            $event->getProduct(),
        ] === [$expectedProduct];

        return $this->isValidEvent(BeforeDeleteProductEvent::class, $callback);
    }

    private function isValidBeforeDeleteBaseProductVariantsEvent(
        ProductInterface $expectedProduct
    ): Constraint {
        $callback = static fn (BeforeDeleteBaseProductVariantsEvent $event): bool => [
            $event->getBaseProduct(),
        ] === [$expectedProduct];

        return $this->isValidEvent(BeforeDeleteBaseProductVariantsEvent::class, $callback);
    }

    private function isValidBeforeUpdateProductEvent(
        ProductUpdateStruct $expectedUpdateStruct
    ): Constraint {
        $callback = static fn (BeforeUpdateProductEvent $event): bool => [
            $event->getUpdateStruct(),
        ] === [$expectedUpdateStruct];

        return $this->isValidEvent(BeforeUpdateProductEvent::class, $callback);
    }

    private function isValidCreateProductEvent(
        ProductCreateStruct $expectedCreateStruct,
        ProductInterface $expectedProduct
    ): Constraint {
        $callback = static fn (CreateProductEvent $event): bool => [
            $event->getCreateStruct(),
            $event->getProduct(),
        ] === [$expectedCreateStruct, $expectedProduct];

        return $this->isValidEvent(CreateProductEvent::class, $callback);
    }

    private function isValidDeleteProductEvent(
        ProductInterface $expectedProduct
    ): Constraint {
        $callback = static fn (DeleteProductEvent $event): bool => [
            $event->getProduct(),
        ] === [$expectedProduct];

        return $this->isValidEvent(DeleteProductEvent::class, $callback);
    }

    private function isValidDeleteBaseProductVariantsEvent(
        ProductInterface $expectedProduct
    ): Constraint {
        $callback = static fn (DeleteBaseProductVariantsEvent $event): bool => [
            $event->getBaseProduct(),
        ] === [$expectedProduct];

        return $this->isValidEvent(DeleteBaseProductVariantsEvent::class, $callback);
    }

    private function isValidUpdateProductEvent(
        ProductInterface $expectedProduct,
        ProductUpdateStruct $expectedUpdateStruct
    ): Constraint {
        $callback = static fn (UpdateProductEvent $event): bool => [
            $event->getProduct(),
            $event->getUpdateStruct(),
        ] == [$expectedProduct, $expectedUpdateStruct];

        return $this->isValidEvent(UpdateProductEvent::class, $callback);
    }

    /**
     * @param iterable<\Ibexa\Contracts\ProductCatalog\Local\Values\Product\ProductVariantCreateStruct> $expectedCreateStructs
     */
    private function isValidBeforeCreateProductVariantsEvent(
        ProductInterface $expectedProduct,
        iterable $expectedCreateStructs
    ): Constraint {
        $callback = static fn (BeforeCreateProductVariantsEvent $event): bool => [
            $event->getProduct(),
            $event->getCreateStructs(),
        ] == [$expectedProduct, $expectedCreateStructs];

        return $this->isValidEvent(BeforeCreateProductVariantsEvent::class, $callback);
    }

    /**
     * @param iterable<\Ibexa\Contracts\ProductCatalog\Local\Values\Product\ProductVariantCreateStruct> $expectedCreateStructs
     */
    private function isValidCreateProductVariantsEvent(
        ProductInterface $expectedProduct,
        iterable $expectedCreateStructs
    ): Constraint {
        $callback = static fn (CreateProductVariantsEvent $event): bool => [
            $event->getProduct(),
            $event->getCreateStructs(),
        ] == [$expectedProduct, $expectedCreateStructs];

        return $this->isValidEvent(CreateProductVariantsEvent::class, $callback);
    }

    private function isValidBeforeUpdateProductVariantEvent(
        ProductVariantInterface $expectedProductVariant,
        ProductVariantUpdateStruct $expectedUpdateStruct
    ): Constraint {
        $callback = static fn (BeforeUpdateProductVariantEvent $event): bool => [
            $event->getProductVariant(),
            $event->getUpdateStruct(),
        ] == [$expectedProductVariant, $expectedUpdateStruct];

        return $this->isValidEvent(BeforeUpdateProductVariantEvent::class, $callback);
    }

    private function isValidUpdateProductVariantEvent(
        ProductVariantInterface $expectedProductVariant,
        ProductVariantUpdateStruct $updateStruct
    ): Constraint {
        $callback = static fn (UpdateProductVariantEvent $event): bool => [
            $event->getProductVariant(),
            $event->getUpdateStruct(),
        ] == [$expectedProductVariant, $updateStruct];

        return $this->isValidEvent(UpdateProductVariantEvent::class, $callback);
    }

    /**
     * @return callable(\Ibexa\Contracts\Core\Repository\Event\BeforeEvent): void
     */
    public function getResultCallbackForProductEvent(ProductInterface $expectedProduct): callable
    {
        return static function (Event $event) use ($expectedProduct): void {
            if ($event instanceof BeforeCreateProductEvent || $event instanceof BeforeUpdateProductEvent) {
                $event->setResultProduct($expectedProduct);
            }
        };
    }

    /**
     * @return callable(\Ibexa\Contracts\Core\Repository\Event\BeforeEvent): void
     */
    public function getResultCallbackForProductVariantEvent(ProductVariantInterface $expectedProductVariant): callable
    {
        return static function (Event $event) use ($expectedProductVariant): void {
            if ($event instanceof BeforeUpdateProductVariantEvent) {
                $event->setResultProductVariant($expectedProductVariant);
            }
        };
    }
}
