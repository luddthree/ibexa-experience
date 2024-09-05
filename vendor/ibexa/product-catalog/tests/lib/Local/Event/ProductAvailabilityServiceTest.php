<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Local\Event;

use Ibexa\Contracts\ProductCatalog\Local\Events\BeforeCreateProductAvailabilityEvent;
use Ibexa\Contracts\ProductCatalog\Local\Events\BeforeDecreaseProductAvailabilityEvent;
use Ibexa\Contracts\ProductCatalog\Local\Events\BeforeDeleteProductAvailabilityEvent;
use Ibexa\Contracts\ProductCatalog\Local\Events\BeforeIncreaseProductAvailabilityEvent;
use Ibexa\Contracts\ProductCatalog\Local\Events\BeforeUpdateProductAvailabilityEvent;
use Ibexa\Contracts\ProductCatalog\Local\Events\CreateProductAvailabilityEvent;
use Ibexa\Contracts\ProductCatalog\Local\Events\DecreaseProductAvailabilityEvent;
use Ibexa\Contracts\ProductCatalog\Local\Events\DeleteProductAvailabilityEvent;
use Ibexa\Contracts\ProductCatalog\Local\Events\IncreaseProductAvailabilityEvent;
use Ibexa\Contracts\ProductCatalog\Local\Events\UpdateProductAvailabilityEvent;
use Ibexa\Contracts\ProductCatalog\ProductAvailabilityServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\Availability\AvailabilityInterface;
use Ibexa\Contracts\ProductCatalog\Values\Availability\ProductAvailabilityCreateStruct;
use Ibexa\Contracts\ProductCatalog\Values\Availability\ProductAvailabilityUpdateStruct;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Ibexa\ProductCatalog\Local\Repository\Event\ProductAvailabilityService;
use PHPUnit\Framework\Constraint\Constraint;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * @template-extends \Ibexa\Tests\ProductCatalog\Local\Event\AbstractEventServiceTest<
 *    \Ibexa\Contracts\ProductCatalog\ProductAvailabilityServiceInterface
 * >
 */
final class ProductAvailabilityServiceTest extends AbstractEventServiceTest
{
    private const STOCK = 10;
    private const AMOUNT = 1;

    private ProductAvailabilityService $service;

    /** @var \Ibexa\Contracts\ProductCatalog\Values\ProductInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $product;

    /** @var \Ibexa\Contracts\ProductCatalog\Values\Availability\ProductAvailabilityUpdateStruct */
    private ProductAvailabilityUpdateStruct $updateStruct;

    private ProductAvailabilityCreateStruct $createStruct;

    /** @var \Ibexa\Contracts\ProductCatalog\Values\Availability\AvailabilityInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $expectedProductAvailability;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new ProductAvailabilityService($this->innerService, $this->eventDispatcher);
        $this->product = $this->createMock(ProductInterface::class);

        $this->createStruct = new ProductAvailabilityCreateStruct(
            $this->product,
            true,
            false,
            self::STOCK
        );
        $this->updateStruct = new ProductAvailabilityUpdateStruct(
            $this->product,
            true
        );

        $this->expectedProductAvailability = $this->createMock(AvailabilityInterface::class);
    }

    protected function getInnerServiceClass(): string
    {
        return ProductAvailabilityServiceInterface::class;
    }

    public function testCreateProductAvailabilityDispatchEvents(): void
    {
        $this->assertInnerServiceIsCalled(
            'createProductAvailability',
            [$this->createStruct],
            $this->expectedProductAvailability
        );
        $this->assertBeforeAndAfterEventsAreDispatched(
            $this->isValidBeforeCreateProductAvailabilityEvent($this->createStruct),
            $this->isValidCreateProductAvailabilityEvent($this->createStruct, $this->expectedProductAvailability)
        );

        $actualProductAvailability = $this->service->createProductAvailability($this->createStruct);

        self::assertSame($this->expectedProductAvailability, $actualProductAvailability);
    }

    public function testCreateProductAvailabilityWhenBeforeEventStoppedPropagation(): void
    {
        $this->assertInnerServiceIsNotCalled(
            'createProductAvailability',
            [$this->createStruct]
        );
        $this->assertBeforeEventIsDispatchedAndStopsPropagation(
            $this->isValidBeforeCreateProductAvailabilityEvent($this->createStruct),
            $this->getResultCallback($this->expectedProductAvailability)
        );

        $actualProductAvailability = $this->service->createProductAvailability($this->createStruct);

        self::assertSame($this->expectedProductAvailability, $actualProductAvailability);
    }

    public function testCreateProductAvailabilityWhenBeforeEventSetsResult(): void
    {
        $this->assertInnerServiceIsNotCalled('createProductAvailability', [$this->createStruct]);
        $this->assertBeforeAndAfterEventsAreDispatchedWithOverwrittenResult(
            $this->isValidBeforeCreateProductAvailabilityEvent($this->createStruct),
            $this->isValidCreateProductAvailabilityEvent($this->createStruct, $this->expectedProductAvailability),
            $this->getResultCallback($this->expectedProductAvailability)
        );

        $actualProductAvailability = $this->service->createProductAvailability($this->createStruct);

        self::assertSame($this->expectedProductAvailability, $actualProductAvailability);
    }

    public function testDeleteProductAvailabilityDispatchEvents(): void
    {
        $this->assertInnerServiceIsCalled('deleteProductAvailability', [$this->product]);
        $this->assertBeforeAndAfterEventsAreDispatched(
            $this->isValidBeforeDeleteProductAvailabilityEvent($this->product),
            $this->isValidDeleteProductAvailabilityEvent($this->product)
        );

        $this->service->deleteProductAvailability($this->product);
    }

    public function testDeleteProductAvailabilityWhenBeforeEventStoppedPropagation(): void
    {
        $this->assertInnerServiceIsNotCalled('deleteProductAvailability', [$this->product]);
        $this->assertBeforeEventIsDispatchedAndStopsPropagation(
            self::isInstanceOf(BeforeDeleteProductAvailabilityEvent::class)
        );

        $this->service->deleteProductAvailability($this->product);
    }

    public function testUpdateProductAvailabilityDispatchEvents(): void
    {
        $this->assertInnerServiceIsCalled(
            'updateProductAvailability',
            [$this->updateStruct],
            $this->expectedProductAvailability
        );
        $this->assertBeforeAndAfterEventsAreDispatched(
            $this->isValidBeforeUpdateProductAvailabilityEvent($this->updateStruct),
            $this->isValidUpdateProductAvailabilityEvent($this->expectedProductAvailability, $this->updateStruct)
        );

        $actualProductAvailability = $this->service->updateProductAvailability($this->updateStruct);

        self::assertSame($this->expectedProductAvailability, $actualProductAvailability);
    }

    public function testUpdateProductAvailabilityWhenBeforeEventStoppedPropagation(): void
    {
        $this->assertInnerServiceIsNotCalled('updateProductAvailability', [$this->updateStruct]);
        $this->assertBeforeEventIsDispatchedAndStopsPropagation(
            self::isInstanceOf(BeforeUpdateProductAvailabilityEvent::class),
            $this->getResultCallback($this->expectedProductAvailability)
        );

        $actualProductAvailability = $this->service->updateProductAvailability($this->updateStruct);

        self::assertSame($this->expectedProductAvailability, $actualProductAvailability);
    }

    public function testUpdateProductAvailabilityWhenBeforeEventSetsResult(): void
    {
        $this->assertInnerServiceIsNotCalled('updateProductAvailability', [$this->updateStruct]);
        $this->assertBeforeAndAfterEventsAreDispatchedWithOverwrittenResult(
            $this->isValidBeforeUpdateProductAvailabilityEvent($this->updateStruct),
            $this->isValidUpdateProductAvailabilityEvent($this->expectedProductAvailability, $this->updateStruct),
            $this->getResultCallback($this->expectedProductAvailability)
        );

        $actualProductAvailability = $this->service->updateProductAvailability($this->updateStruct);

        self::assertSame($this->expectedProductAvailability, $actualProductAvailability);
    }

    public function testIncreaseProductAvailabilityDispatchEvents(): void
    {
        $this->assertInnerServiceIsCalled(
            'increaseProductAvailability',
            [$this->product, self::AMOUNT],
            $this->expectedProductAvailability
        );
        $this->assertBeforeAndAfterEventsAreDispatched(
            $this->isValidBeforeIncreaseProductAvailabilityEvent($this->product, self::AMOUNT),
            $this->isValidIncreaseProductAvailabilityEvent(
                $this->expectedProductAvailability,
                $this->product,
                self::AMOUNT
            )
        );

        $actualProductAvailability = $this->service->increaseProductAvailability($this->product, self::AMOUNT);

        self::assertSame($this->expectedProductAvailability, $actualProductAvailability);
    }

    public function testIncreaseProductAvailabilityWhenBeforeEventStoppedPropagation(): void
    {
        $this->assertInnerServiceIsNotCalled('increaseProductAvailability', [$this->product, self::AMOUNT]);
        $this->assertBeforeEventIsDispatchedAndStopsPropagation(
            self::isInstanceOf(BeforeIncreaseProductAvailabilityEvent::class),
            $this->getResultCallback($this->expectedProductAvailability)
        );

        $actualProductAvailability = $this->service->increaseProductAvailability($this->product, self::AMOUNT);

        self::assertSame($this->expectedProductAvailability, $actualProductAvailability);
    }

    public function testIncreaseProductAvailabilityWhenBeforeEventSetsResult(): void
    {
        $this->assertInnerServiceIsNotCalled('increaseProductAvailability', [$this->product, self::AMOUNT]);
        $this->assertBeforeAndAfterEventsAreDispatchedWithOverwrittenResult(
            $this->isValidBeforeIncreaseProductAvailabilityEvent($this->product, self::AMOUNT),
            $this->isValidIncreaseProductAvailabilityEvent(
                $this->expectedProductAvailability,
                $this->product,
                self::AMOUNT
            ),
            $this->getResultCallback($this->expectedProductAvailability)
        );

        $actualProductAvailability = $this->service->increaseProductAvailability($this->product, self::AMOUNT);

        self::assertSame($this->expectedProductAvailability, $actualProductAvailability);
    }

    public function testDecreaseProductAvailabilityDispatchEvents(): void
    {
        $this->assertInnerServiceIsCalled(
            'decreaseProductAvailability',
            [$this->product, self::AMOUNT],
            $this->expectedProductAvailability
        );
        $this->assertBeforeAndAfterEventsAreDispatched(
            $this->isValidBeforeDecreaseProductAvailabilityEvent($this->product, self::AMOUNT),
            $this->isValidDecreaseProductAvailabilityEvent(
                $this->expectedProductAvailability,
                $this->product,
                self::AMOUNT
            )
        );

        $actualProductAvailability = $this->service->decreaseProductAvailability($this->product, self::AMOUNT);

        self::assertSame($this->expectedProductAvailability, $actualProductAvailability);
    }

    public function testDecreaseProductAvailabilityWhenBeforeEventStoppedPropagation(): void
    {
        $this->assertInnerServiceIsNotCalled('decreaseProductAvailability', [$this->product, self::AMOUNT]);
        $this->assertBeforeEventIsDispatchedAndStopsPropagation(
            self::isInstanceOf(BeforeDecreaseProductAvailabilityEvent::class),
            $this->getResultCallback($this->expectedProductAvailability)
        );

        $actualProductAvailability = $this->service->decreaseProductAvailability($this->product, self::AMOUNT);

        self::assertSame($this->expectedProductAvailability, $actualProductAvailability);
    }

    public function testDecreaseProductAvailabilityWhenBeforeEventSetsResult(): void
    {
        $this->assertInnerServiceIsNotCalled('decreaseProductAvailability', [$this->product, self::AMOUNT]);
        $this->assertBeforeAndAfterEventsAreDispatchedWithOverwrittenResult(
            $this->isValidBeforeDecreaseProductAvailabilityEvent($this->product, self::AMOUNT),
            $this->isValidDecreaseProductAvailabilityEvent(
                $this->expectedProductAvailability,
                $this->product,
                self::AMOUNT
            ),
            $this->getResultCallback($this->expectedProductAvailability)
        );

        $actualProductAvailability = $this->service->decreaseProductAvailability($this->product, self::AMOUNT);

        self::assertSame($this->expectedProductAvailability, $actualProductAvailability);
    }

    private function isValidBeforeCreateProductAvailabilityEvent(
        ProductAvailabilityCreateStruct $expectedCreateStruct
    ): Constraint {
        $callback = static fn (BeforeCreateProductAvailabilityEvent $event): bool => [
            $event->getCreateStruct(),
        ] === [$expectedCreateStruct];

        return $this->isValidEvent(BeforeCreateProductAvailabilityEvent::class, $callback);
    }

    private function isValidBeforeDeleteProductAvailabilityEvent(
        ProductInterface $expectedProduct
    ): Constraint {
        $callback = static fn (BeforeDeleteProductAvailabilityEvent $event): bool => [
            $event->getProduct(),
        ] === [$expectedProduct];

        return $this->isValidEvent(BeforeDeleteProductAvailabilityEvent::class, $callback);
    }

    private function isValidBeforeUpdateProductAvailabilityEvent(
        ProductAvailabilityUpdateStruct $expectedUpdateStruct
    ): Constraint {
        $callback = static fn (BeforeUpdateProductAvailabilityEvent $event): bool => [
            $event->getUpdateStruct(),
        ] === [$expectedUpdateStruct];

        return $this->isValidEvent(BeforeUpdateProductAvailabilityEvent::class, $callback);
    }

    private function isValidCreateProductAvailabilityEvent(
        ProductAvailabilityCreateStruct $expectedCreateStruct,
        AvailabilityInterface $expectedProductAvailability
    ): Constraint {
        $callback = static fn (CreateProductAvailabilityEvent $event): bool => [
            $event->getCreateStruct(),
            $event->getProductAvailability(),
        ] === [$expectedCreateStruct, $expectedProductAvailability];

        return $this->isValidEvent(CreateProductAvailabilityEvent::class, $callback);
    }

    private function isValidDeleteProductAvailabilityEvent(
        ProductInterface $expectedProduct
    ): Constraint {
        $callback = static fn (DeleteProductAvailabilityEvent $event): bool => [
            $event->getProduct(),
        ] === [$expectedProduct];

        return $this->isValidEvent(DeleteProductAvailabilityEvent::class, $callback);
    }

    private function isValidUpdateProductAvailabilityEvent(
        AvailabilityInterface $expectedProductAvailability,
        ProductAvailabilityUpdateStruct $expectedUpdateStruct
    ): Constraint {
        $callback = static fn (UpdateProductAvailabilityEvent $event): bool => [
            $event->getProductAvailability(),
            $event->getUpdateStruct(),
        ] == [$expectedProductAvailability, $expectedUpdateStruct];

        return $this->isValidEvent(UpdateProductAvailabilityEvent::class, $callback);
    }

    private function isValidBeforeIncreaseProductAvailabilityEvent(
        ProductInterface $expectedProduct,
        int $expectedAmount
    ): Constraint {
        $callback = static fn (BeforeIncreaseProductAvailabilityEvent $event): bool => [
                $event->getProduct(),
                $event->getAmount(),
            ] === [$expectedProduct, $expectedAmount];

        return $this->isValidEvent(BeforeIncreaseProductAvailabilityEvent::class, $callback);
    }

    private function isValidIncreaseProductAvailabilityEvent(
        AvailabilityInterface $expectedProductAvailability,
        ProductInterface $expectedProduct,
        int $expectedAmount
    ): Constraint {
        $callback = static fn (IncreaseProductAvailabilityEvent $event): bool => [
                $event->getProductAvailability(),
                $event->getProduct(),
                $event->getAmount(),
            ] == [$expectedProductAvailability, $expectedProduct, $expectedAmount];

        return $this->isValidEvent(IncreaseProductAvailabilityEvent::class, $callback);
    }

    private function isValidBeforeDecreaseProductAvailabilityEvent(
        ProductInterface $expectedProduct,
        int $expectedAmount
    ): Constraint {
        $callback = static fn (BeforeDecreaseProductAvailabilityEvent $event): bool => [
                $event->getProduct(),
                $event->getAmount(),
            ] === [$expectedProduct, $expectedAmount];

        return $this->isValidEvent(BeforeDecreaseProductAvailabilityEvent::class, $callback);
    }

    private function isValidDecreaseProductAvailabilityEvent(
        AvailabilityInterface $expectedProductAvailability,
        ProductInterface $expectedProduct,
        int $expectedAmount
    ): Constraint {
        $callback = static fn (DecreaseProductAvailabilityEvent $event): bool => [
                $event->getProductAvailability(),
                $event->getProduct(),
                $event->getAmount(),
            ] == [$expectedProductAvailability, $expectedProduct, $expectedAmount];

        return $this->isValidEvent(DecreaseProductAvailabilityEvent::class, $callback);
    }

    /**
     * @return callable(\Ibexa\Contracts\Core\Repository\Event\BeforeEvent): void
     */
    private function getResultCallback(AvailabilityInterface $expectedProductAvailability): callable
    {
        return static function (Event $event) use ($expectedProductAvailability): void {
            if (
                $event instanceof BeforeCreateProductAvailabilityEvent
                || $event instanceof BeforeUpdateProductAvailabilityEvent
                || $event instanceof BeforeIncreaseProductAvailabilityEvent
                || $event instanceof BeforeDecreaseProductAvailabilityEvent
            ) {
                $event->setResultProductAvailability($expectedProductAvailability);
            }
        };
    }
}
