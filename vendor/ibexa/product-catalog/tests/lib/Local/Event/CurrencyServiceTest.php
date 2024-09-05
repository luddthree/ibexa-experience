<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Local\Event;

use Ibexa\Contracts\ProductCatalog\CurrencyServiceInterface;
use Ibexa\Contracts\ProductCatalog\Events\BeforeCreateCurrencyEvent;
use Ibexa\Contracts\ProductCatalog\Events\BeforeDeleteCurrencyEvent;
use Ibexa\Contracts\ProductCatalog\Events\BeforeUpdateCurrencyEvent;
use Ibexa\Contracts\ProductCatalog\Events\CreateCurrencyEvent;
use Ibexa\Contracts\ProductCatalog\Events\DeleteCurrencyEvent;
use Ibexa\Contracts\ProductCatalog\Events\UpdateCurrencyEvent;
use Ibexa\Contracts\ProductCatalog\Values\Currency\CurrencyCreateStruct;
use Ibexa\Contracts\ProductCatalog\Values\Currency\CurrencyUpdateStruct;
use Ibexa\Contracts\ProductCatalog\Values\CurrencyInterface;
use Ibexa\ProductCatalog\Local\Repository\Event\CurrencyService;
use PHPUnit\Framework\Constraint\Constraint;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * @template-extends \Ibexa\Tests\ProductCatalog\Local\Event\AbstractEventServiceTest<
 *      \Ibexa\Contracts\ProductCatalog\CurrencyServiceInterface
 * >
 */
final class CurrencyServiceTest extends AbstractEventServiceTest
{
    private CurrencyService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new CurrencyService($this->innerService, $this->eventDispatcher);
    }

    public function testCreateCurrencyDispatchEvents(): void
    {
        $createStruct = new CurrencyCreateStruct('EUR', 2, true);
        $expectedCurrency = $this->createMock(CurrencyInterface::class);

        $this->assertInnerServiceIsCalled('createCurrency', [$createStruct], $expectedCurrency);
        $this->assertBeforeAndAfterEventsAreDispatched(
            $this->isValidBeforeCreateCurrencyEvent($createStruct),
            $this->isValidCreateCurrencyEvent($createStruct, $expectedCurrency)
        );

        $actualCurrency = $this->service->createCurrency($createStruct);

        self::assertSame($expectedCurrency, $actualCurrency);
    }

    public function testCreateCurrencyWhenBeforeEventStoppedPropagation(): void
    {
        $createStruct = new CurrencyCreateStruct('EUR', 2, true);
        $expectedCurrency = $this->createMock(CurrencyInterface::class);

        $this->assertInnerServiceIsNotCalled('createCurrency');
        $this->assertBeforeEventIsDispatchedAndStopsPropagation(
            $this->isValidBeforeCreateCurrencyEvent($createStruct),
            $this->getResultCallback($expectedCurrency)
        );

        $actualCurrency = $this->service->createCurrency($createStruct);

        self::assertSame($expectedCurrency, $actualCurrency);
    }

    public function testCreateCurrencyWhenBeforeEventSetsResult(): void
    {
        $createStruct = new CurrencyCreateStruct('EUR', 2, true);
        $expectedCurrency = $this->createMock(CurrencyInterface::class);

        $this->assertInnerServiceIsNotCalled('createCurrency', [$createStruct]);
        $this->assertBeforeAndAfterEventsAreDispatchedWithOverwrittenResult(
            $this->isValidBeforeCreateCurrencyEvent($createStruct),
            $this->isValidCreateCurrencyEvent($createStruct, $expectedCurrency),
            $this->getResultCallback($expectedCurrency)
        );

        $actualCurrency = $this->service->createCurrency($createStruct);

        self::assertSame($expectedCurrency, $actualCurrency);
    }

    public function testDeleteCurrencyDispatchEvents(): void
    {
        $currency = $this->createMock(CurrencyInterface::class);

        $this->assertInnerServiceIsCalled('deleteCurrency', [$currency]);
        $this->assertBeforeAndAfterEventsAreDispatched(
            $this->isValidBeforeDeleteCurrencyEvent($currency),
            $this->isValidDeleteCurrencyEvent($currency)
        );

        $this->service->deleteCurrency($currency);
    }

    public function testDeleteCurrencyWhenBeforeEventStoppedPropagation(): void
    {
        $Currency = $this->createMock(CurrencyInterface::class);

        $this->assertInnerServiceIsNotCalled('deleteCurrency', [$Currency]);
        $this->assertBeforeEventIsDispatchedAndStopsPropagation(
            self::isInstanceOf(BeforeDeleteCurrencyEvent::class)
        );

        $this->service->deleteCurrency($Currency);
    }

    public function testUpdateCurrencyDispatchEvents(): void
    {
        $currency = $this->createMock(CurrencyInterface::class);
        $updateStruct = new CurrencyUpdateStruct();
        $expectedCurrency = $this->createMock(CurrencyInterface::class);

        $this->assertInnerServiceIsCalled('updateCurrency', [$currency, $updateStruct], $expectedCurrency);
        $this->assertBeforeAndAfterEventsAreDispatched(
            $this->isValidBeforeUpdateCurrencyEvent($currency, $updateStruct),
            $this->isValidUpdateCurrencyEvent($currency, $updateStruct)
        );

        $actualCurrency = $this->service->updateCurrency($currency, $updateStruct);

        self::assertSame($expectedCurrency, $actualCurrency);
    }

    public function testUpdateCurrencyWhenBeforeEventStoppedPropagation(): void
    {
        $currency = $this->createMock(CurrencyInterface::class);
        $updateStruct = new CurrencyUpdateStruct();
        $expectedCurrency = $this->createMock(CurrencyInterface::class);

        $this->assertInnerServiceIsNotCalled('updateCurrency', [$currency, $updateStruct]);
        $this->assertBeforeEventIsDispatchedAndStopsPropagation(
            self::isInstanceOf(BeforeUpdateCurrencyEvent::class),
            $this->getResultCallback($expectedCurrency)
        );

        $actualCurrency = $this->service->updateCurrency($currency, $updateStruct);

        self::assertSame($expectedCurrency, $actualCurrency);
    }

    public function testUpdateCurrencyWhenBeforeEventSetsResult(): void
    {
        $currency = $this->createMock(CurrencyInterface::class);
        $updateStruct = new CurrencyUpdateStruct();
        $expectedCurrency = $this->createMock(CurrencyInterface::class);

        $this->assertInnerServiceIsNotCalled('updateCurrency', [$updateStruct]);
        $this->assertBeforeAndAfterEventsAreDispatchedWithOverwrittenResult(
            $this->isValidBeforeUpdateCurrencyEvent($currency, $updateStruct),
            $this->isValidUpdateCurrencyEvent($currency, $updateStruct),
            $this->getResultCallback($expectedCurrency)
        );

        $actualCurrency = $this->service->updateCurrency($currency, $updateStruct);

        self::assertSame($expectedCurrency, $actualCurrency);
    }

    private function isValidBeforeCreateCurrencyEvent(
        CurrencyCreateStruct $expectedCreateStruct
    ): Constraint {
        $callback = static fn (BeforeCreateCurrencyEvent $event): bool => [
            $event->getCreateStruct(),
        ] === [$expectedCreateStruct];

        return $this->isValidEvent(BeforeCreateCurrencyEvent::class, $callback);
    }

    private function isValidBeforeDeleteCurrencyEvent(
        CurrencyInterface $expectedCurrency
    ): Constraint {
        $callback = static fn (BeforeDeleteCurrencyEvent $event): bool => [
            $event->getCurrency(),
        ] === [$expectedCurrency];

        return $this->isValidEvent(BeforeDeleteCurrencyEvent::class, $callback);
    }

    private function isValidBeforeUpdateCurrencyEvent(
        CurrencyInterface $currency,
        CurrencyUpdateStruct $expectedUpdateStruct
    ): Constraint {
        $callback = static fn (BeforeUpdateCurrencyEvent $event): bool => [
            $event->getCurrency(),
            $event->getUpdateStruct(),
        ] === [$currency, $expectedUpdateStruct];

        return $this->isValidEvent(BeforeUpdateCurrencyEvent::class, $callback);
    }

    private function isValidCreateCurrencyEvent(
        CurrencyCreateStruct $expectedCreateStruct,
        CurrencyInterface $expectedCurrency
    ): Constraint {
        $callback = static fn (CreateCurrencyEvent $event): bool => [
            $event->getCreateStruct(),
            $event->getCurrency(),
        ] === [$expectedCreateStruct, $expectedCurrency];

        return $this->isValidEvent(CreateCurrencyEvent::class, $callback);
    }

    private function isValidDeleteCurrencyEvent(
        CurrencyInterface $expectedCurrency
    ): Constraint {
        $callback = static fn (DeleteCurrencyEvent $event): bool => [
            $event->getCurrency(),
        ] === [$expectedCurrency];

        return $this->isValidEvent(DeleteCurrencyEvent::class, $callback);
    }

    private function isValidUpdateCurrencyEvent(
        CurrencyInterface $currency,
        CurrencyUpdateStruct $expectedUpdateStruct
    ): Constraint {
        $callback = static fn (UpdateCurrencyEvent $event): bool => [
            $event->getCurrency(),
            $event->getUpdateStruct(),
        ] == [$currency, $expectedUpdateStruct];

        return $this->isValidEvent(UpdateCurrencyEvent::class, $callback);
    }

    /**
     * @return callable(\Ibexa\Contracts\Core\Repository\Event\BeforeEvent): void
     */
    private function getResultCallback(CurrencyInterface $expectedCurrency): callable
    {
        return static function (Event $event) use ($expectedCurrency): void {
            if ($event instanceof BeforeCreateCurrencyEvent ||
                $event instanceof BeforeUpdateCurrencyEvent) {
                $event->setResultCurrency($expectedCurrency);
            }
        };
    }

    protected function getInnerServiceClass(): string
    {
        return CurrencyServiceInterface::class;
    }
}
