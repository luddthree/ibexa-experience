<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Local\Event;

use Ibexa\Contracts\ProductCatalog\Events\BeforeCreatePriceEvent;
use Ibexa\Contracts\ProductCatalog\Events\BeforeDeletePriceEvent;
use Ibexa\Contracts\ProductCatalog\Events\BeforeExecutePriceStructsEvent;
use Ibexa\Contracts\ProductCatalog\Events\BeforeUpdatePriceEvent;
use Ibexa\Contracts\ProductCatalog\Events\CreatePriceEvent;
use Ibexa\Contracts\ProductCatalog\Events\DeletePriceEvent;
use Ibexa\Contracts\ProductCatalog\Events\ExecutePriceStructsEvent;
use Ibexa\Contracts\ProductCatalog\Events\UpdatePriceEvent;
use Ibexa\Contracts\ProductCatalog\ProductPriceServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\Price\Create\Struct\ProductPriceCreateStructInterface;
use Ibexa\Contracts\ProductCatalog\Values\Price\Delete\Struct\ProductPriceDeleteStructInterface;
use Ibexa\Contracts\ProductCatalog\Values\Price\ProductPriceStructInterface;
use Ibexa\Contracts\ProductCatalog\Values\Price\Update\Struct\ProductPriceUpdateStructInterface;
use Ibexa\Contracts\ProductCatalog\Values\PriceInterface;
use Ibexa\ProductCatalog\Local\Repository\Event\ProductPriceService;
use PHPUnit\Framework\Constraint\Constraint;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * @template-extends \Ibexa\Tests\ProductCatalog\Local\Event\AbstractEventServiceTest<
 *    \Ibexa\Contracts\ProductCatalog\ProductPriceServiceInterface
 * >
 */
final class ProductPriceServiceTest extends AbstractEventServiceTest
{
    private ProductPriceService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new ProductPriceService($this->innerService, $this->eventDispatcher);
    }

    public function testCreatePriceDispatchEvents(): void
    {
        $createStruct = $this->createMock(ProductPriceCreateStructInterface::class);
        $expectedPrice = $this->createMock(PriceInterface::class);

        $this->assertInnerServiceIsCalled('createProductPrice', [$createStruct], $expectedPrice);
        $this->assertBeforeAndAfterEventsAreDispatched(
            $this->isValidBeforeCreatePriceEvent($createStruct),
            $this->isValidCreatePriceEvent($createStruct, $expectedPrice)
        );

        $actualPrice = $this->service->createProductPrice($createStruct);

        self::assertSame($expectedPrice, $actualPrice);
    }

    public function testCreatePriceWhenBeforeEventStoppedPropagation(): void
    {
        $createStruct = $this->createMock(ProductPriceCreateStructInterface::class);
        $expectedPrice = $this->createMock(PriceInterface::class);

        $this->assertInnerServiceIsNotCalled('createProductPrice');
        $this->assertBeforeEventIsDispatchedAndStopsPropagation(
            $this->isValidBeforeCreatePriceEvent($createStruct),
            $this->getResultCallback($expectedPrice)
        );

        $actualPrice = $this->service->createProductPrice($createStruct);

        self::assertSame($expectedPrice, $actualPrice);
    }

    public function testCreatePriceWhenBeforeEventSetsResult(): void
    {
        $createStruct = $this->createMock(ProductPriceCreateStructInterface::class);
        $expectedPrice = $this->createMock(PriceInterface::class);

        $this->assertInnerServiceIsNotCalled('createProductPrice', [$createStruct]);
        $this->assertBeforeAndAfterEventsAreDispatchedWithOverwrittenResult(
            $this->isValidBeforeCreatePriceEvent($createStruct),
            $this->isValidCreatePriceEvent($createStruct, $expectedPrice),
            $this->getResultCallback($expectedPrice)
        );

        $actualPrice = $this->service->createProductPrice($createStruct);

        self::assertSame($expectedPrice, $actualPrice);
    }

    public function testDeletePriceDispatchEvents(): void
    {
        $deleteStruct = $this->createMock(ProductPriceDeleteStructInterface::class);

        $this->assertInnerServiceIsCalled('deletePrice', [$deleteStruct]);
        $this->assertBeforeAndAfterEventsAreDispatched(
            $this->isValidBeforeDeletePriceEvent($deleteStruct),
            $this->isValidDeletePriceEvent($deleteStruct)
        );

        $this->service->deletePrice($deleteStruct);
    }

    public function testDeletePriceWhenBeforeEventStoppedPropagation(): void
    {
        $deleteStruct = $this->createMock(ProductPriceDeleteStructInterface::class);

        $this->assertInnerServiceIsNotCalled('deletePrice', [$deleteStruct]);
        $this->assertBeforeEventIsDispatchedAndStopsPropagation(
            self::isInstanceOf(BeforeDeletePriceEvent::class)
        );

        $this->service->deletePrice($deleteStruct);
    }

    public function testExecuteDispatchEvents(): void
    {
        $structs = [
            $this->createMock(ProductPriceStructInterface::class),
            $this->createMock(ProductPriceStructInterface::class),
            $this->createMock(ProductPriceStructInterface::class),
        ];

        $this->assertInnerServiceIsCalled('execute', [$structs]);
        $this->assertBeforeAndAfterEventsAreDispatched(
            $this->isValidBeforeExecutePriceStructsEvent($structs),
            $this->isValidExecutePriceStructsEvent($structs)
        );

        $this->service->execute($structs);
    }

    public function testExecuteDispatchEventsWhenBeforeEventStoppedPropagation(): void
    {
        $structs = [
            $this->createMock(ProductPriceStructInterface::class),
            $this->createMock(ProductPriceStructInterface::class),
            $this->createMock(ProductPriceStructInterface::class),
        ];

        $this->assertInnerServiceIsNotCalled('execute', [$structs]);
        $this->assertBeforeEventIsDispatchedAndStopsPropagation(
            self::isInstanceOf(BeforeExecutePriceStructsEvent::class)
        );

        $this->service->execute($structs);
    }

    public function testUpdatePriceDispatchEvents(): void
    {
        $updateStruct = $this->createMock(ProductPriceUpdateStructInterface::class);
        $expectedPrice = $this->createMock(PriceInterface::class);

        $this->assertInnerServiceIsCalled('updateProductPrice', [$updateStruct], $expectedPrice);
        $this->assertBeforeAndAfterEventsAreDispatched(
            $this->isValidBeforeUpdatePriceEvent($updateStruct),
            $this->isValidUpdatePriceEvent($updateStruct, $expectedPrice)
        );

        $actualPrice = $this->service->updateProductPrice($updateStruct);

        self::assertSame($expectedPrice, $actualPrice);
    }

    public function testUpdatePriceWhenBeforeEventStoppedPropagation(): void
    {
        $updateStruct = $this->createMock(ProductPriceUpdateStructInterface::class);
        $expectedPrice = $this->createMock(PriceInterface::class);

        $this->assertInnerServiceIsNotCalled('updateProductPrice', [$updateStruct]);
        $this->assertBeforeEventIsDispatchedAndStopsPropagation(
            self::isInstanceOf(BeforeUpdatePriceEvent::class),
            $this->getResultCallback($expectedPrice)
        );

        $actualPrice = $this->service->updateProductPrice($updateStruct);

        self::assertSame($expectedPrice, $actualPrice);
    }

    public function testUpdatePriceWhenBeforeEventSetsResult(): void
    {
        $updateStruct = $this->createMock(ProductPriceUpdateStructInterface::class);
        $expectedPrice = $this->createMock(PriceInterface::class);

        $this->assertInnerServiceIsNotCalled('updateProductPrice', [$updateStruct]);
        $this->assertBeforeAndAfterEventsAreDispatchedWithOverwrittenResult(
            $this->isValidBeforeUpdatePriceEvent($updateStruct),
            $this->isValidUpdatePriceEvent($updateStruct, $expectedPrice),
            $this->getResultCallback($expectedPrice)
        );

        $actualPrice = $this->service->updateProductPrice($updateStruct);

        self::assertSame($expectedPrice, $actualPrice);
    }

    protected function getInnerServiceClass(): string
    {
        return ProductPriceServiceInterface::class;
    }

    private function isValidBeforeCreatePriceEvent(
        ProductPriceCreateStructInterface $expectedCreateStruct
    ): Constraint {
        $callback = static fn (BeforeCreatePriceEvent $event): bool => [
            $event->getCreateStruct(),
        ] === [$expectedCreateStruct];

        return $this->isValidEvent(BeforeCreatePriceEvent::class, $callback);
    }

    private function isValidBeforeDeletePriceEvent(
        ProductPriceDeleteStructInterface $expectedDeleteStruct
    ): Constraint {
        $callback = static fn (BeforeDeletePriceEvent $event): bool => [
            $event->getDeleteStruct(),
        ] === [$expectedDeleteStruct];

        return $this->isValidEvent(BeforeDeletePriceEvent::class, $callback);
    }

    /**
     * @param iterable<\Ibexa\Contracts\ProductCatalog\Values\Price\ProductPriceStructInterface> $expectedStructs
     */
    private function isValidBeforeExecutePriceStructsEvent(
        iterable $expectedStructs
    ): Constraint {
        $callback = static fn (BeforeExecutePriceStructsEvent $event): bool => [
            $event->getPriceStructs(),
        ] === [$expectedStructs];

        return $this->isValidEvent(BeforeExecutePriceStructsEvent::class, $callback);
    }

    private function isValidBeforeUpdatePriceEvent(
        ProductPriceUpdateStructInterface $expectedUpdateStruct
    ): Constraint {
        $callback = static fn (BeforeUpdatePriceEvent $event): bool => [
            $event->getUpdateStruct(),
        ] === [$expectedUpdateStruct];

        return $this->isValidEvent(BeforeUpdatePriceEvent::class, $callback);
    }

    private function isValidCreatePriceEvent(
        ProductPriceCreateStructInterface $expectedCreateStruct,
        PriceInterface $expectedPrice
    ): Constraint {
        $callback = static fn (CreatePriceEvent $event): bool => [
            $event->getCreateStruct(),
            $event->getPrice(),
        ] === [$expectedCreateStruct, $expectedPrice];

        return $this->isValidEvent(CreatePriceEvent::class, $callback);
    }

    private function isValidDeletePriceEvent(
        ProductPriceDeleteStructInterface $expectedDeleteStruct
    ): Constraint {
        $callback = static fn (DeletePriceEvent $event): bool => [
            $event->getDeleteStruct(),
        ] === [$expectedDeleteStruct];

        return $this->isValidEvent(DeletePriceEvent::class, $callback);
    }

    /**
     * @param iterable<\Ibexa\Contracts\ProductCatalog\Values\Price\ProductPriceStructInterface> $expectedStructs
     */
    private function isValidExecutePriceStructsEvent(
        iterable $expectedStructs
    ): Constraint {
        $callback = static fn (ExecutePriceStructsEvent $event): bool => [
            $event->getPriceStructs(),
        ] === [$expectedStructs];

        return $this->isValidEvent(ExecutePriceStructsEvent::class, $callback);
    }

    private function isValidUpdatePriceEvent(
        ProductPriceUpdateStructInterface $expectedUpdateStruct,
        PriceInterface $expectedPrice
    ): Constraint {
        $callback = static fn (UpdatePriceEvent $event): bool => [
            $event->getUpdateStruct(),
            $event->getPrice(),
        ] === [$expectedUpdateStruct, $expectedPrice];

        return $this->isValidEvent(UpdatePriceEvent::class, $callback);
    }

    /**
     * @return callable(\Ibexa\Contracts\Core\Repository\Event\BeforeEvent): void
     */
    private function getResultCallback(PriceInterface $expectedPrice): callable
    {
        return static function (Event $event) use ($expectedPrice): void {
            if ($event instanceof BeforeCreatePriceEvent || $event instanceof BeforeUpdatePriceEvent) {
                $event->setResultPrice($expectedPrice);
            }
        };
    }
}
