<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Local\Event;

use Ibexa\Contracts\ProductCatalog\Local\Events\BeforeCreateAttributeGroupEvent;
use Ibexa\Contracts\ProductCatalog\Local\Events\BeforeDeleteAttributeGroupEvent;
use Ibexa\Contracts\ProductCatalog\Local\Events\BeforeUpdateAttributeGroupEvent;
use Ibexa\Contracts\ProductCatalog\Local\Events\CreateAttributeGroupEvent;
use Ibexa\Contracts\ProductCatalog\Local\Events\DeleteAttributeGroupEvent;
use Ibexa\Contracts\ProductCatalog\Local\Events\UpdateAttributeGroupEvent;
use Ibexa\Contracts\ProductCatalog\Local\LocalAttributeGroupServiceInterface;
use Ibexa\Contracts\ProductCatalog\Local\Values\AttributeGroup\AttributeGroupCreateStruct;
use Ibexa\Contracts\ProductCatalog\Local\Values\AttributeGroup\AttributeGroupUpdateStruct;
use Ibexa\Contracts\ProductCatalog\Values\AttributeGroupInterface;
use Ibexa\ProductCatalog\Local\Repository\Event\AttributeGroupService;
use PHPUnit\Framework\Constraint\Constraint;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * @template-extends \Ibexa\Tests\ProductCatalog\Local\Event\AbstractEventServiceTest<
 *    \Ibexa\Contracts\ProductCatalog\Local\LocalAttributeGroupServiceInterface
 * >
 */
final class AttributeGroupServiceTest extends AbstractEventServiceTest
{
    private AttributeGroupService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new AttributeGroupService($this->innerService, $this->eventDispatcher);
    }

    protected function getInnerServiceClass(): string
    {
        return LocalAttributeGroupServiceInterface::class;
    }

    public function testCreateAttributeGroupDispatchEvents(): void
    {
        $createStruct = new AttributeGroupCreateStruct('foo');
        $expectedAttributeGroup = $this->createMock(AttributeGroupInterface::class);

        $this->assertInnerServiceIsCalled('createAttributeGroup', [$createStruct], $expectedAttributeGroup);
        $this->assertBeforeAndAfterEventsAreDispatched(
            $this->isValidBeforeCreateAttributeGroupEvent($createStruct),
            $this->isValidCreateAttributeGroupEvent($createStruct, $expectedAttributeGroup)
        );

        $actualAttributeGroup = $this->service->createAttributeGroup($createStruct);

        self::assertSame($expectedAttributeGroup, $actualAttributeGroup);
    }

    public function testCreateAttributeGroupWhenBeforeEventStoppedPropagation(): void
    {
        $createStruct = new AttributeGroupCreateStruct('foo');
        $expectedAttributeGroup = $this->createMock(AttributeGroupInterface::class);

        $this->assertInnerServiceIsNotCalled('createAttributeGroup');
        $this->assertBeforeEventIsDispatchedAndStopsPropagation(
            $this->isValidBeforeCreateAttributeGroupEvent($createStruct),
            $this->getResultCallback($expectedAttributeGroup)
        );

        $actualAttributeGroup = $this->service->createAttributeGroup($createStruct);

        self::assertSame($expectedAttributeGroup, $actualAttributeGroup);
    }

    public function testCreateAttributeGroupWhenBeforeEventSetsResult(): void
    {
        $createStruct = new AttributeGroupCreateStruct('foo');
        $expectedAttributeGroup = $this->createMock(AttributeGroupInterface::class);

        $this->assertInnerServiceIsNotCalled('createAttributeGroup', [$createStruct]);
        $this->assertBeforeAndAfterEventsAreDispatchedWithOverwrittenResult(
            $this->isValidBeforeCreateAttributeGroupEvent($createStruct),
            $this->isValidCreateAttributeGroupEvent($createStruct, $expectedAttributeGroup),
            $this->getResultCallback($expectedAttributeGroup)
        );

        $actualAttributeGroup = $this->service->createAttributeGroup($createStruct);

        self::assertSame($expectedAttributeGroup, $actualAttributeGroup);
    }

    public function testDeleteAttributeGroupDispatchEvents(): void
    {
        $attributeGroup = $this->createMock(AttributeGroupInterface::class);

        $this->assertInnerServiceIsCalled('deleteAttributeGroup', [$attributeGroup]);
        $this->assertBeforeAndAfterEventsAreDispatched(
            $this->isValidBeforeDeleteAttributeGroupEvent($attributeGroup),
            $this->isValidDeleteAttributeGroupEvent($attributeGroup)
        );

        $this->service->deleteAttributeGroup($attributeGroup);
    }

    public function testDeleteAttributeGroupWhenBeforeEventStoppedPropagation(): void
    {
        $attributeGroup = $this->createMock(AttributeGroupInterface::class);

        $this->assertInnerServiceIsNotCalled('deleteAttributeGroup', [$attributeGroup]);
        $this->assertBeforeEventIsDispatchedAndStopsPropagation(
            self::isInstanceOf(BeforeDeleteAttributeGroupEvent::class)
        );

        $this->service->deleteAttributeGroup($attributeGroup);
    }

    public function testUpdateAttributeGroupDispatchEvents(): void
    {
        $attributeGroup = $this->createMock(AttributeGroupInterface::class);
        $updateStruct = new AttributeGroupUpdateStruct();
        $expectedAttributeGroup = $this->createMock(AttributeGroupInterface::class);

        $this->assertInnerServiceIsCalled(
            'updateAttributeGroup',
            [$attributeGroup, $updateStruct],
            $expectedAttributeGroup
        );

        $this->assertBeforeAndAfterEventsAreDispatched(
            $this->isValidBeforeUpdateAttributeGroupEvent($attributeGroup, $updateStruct),
            $this->isValidUpdateAttributeGroupEvent($attributeGroup, $updateStruct)
        );

        $actualAttributeGroup = $this->service->updateAttributeGroup($attributeGroup, $updateStruct);

        self::assertSame($expectedAttributeGroup, $actualAttributeGroup);
    }

    public function testUpdateAttributeGroupWhenBeforeEventStoppedPropagation(): void
    {
        $attributeGroup = $this->createMock(AttributeGroupInterface::class);
        $updateStruct = new AttributeGroupUpdateStruct();
        $expectedAttributeGroup = $this->createMock(AttributeGroupInterface::class);

        $this->assertInnerServiceIsNotCalled('updateAttributeGroup', [$attributeGroup, $updateStruct]);
        $this->assertBeforeEventIsDispatchedAndStopsPropagation(
            self::isInstanceOf(BeforeUpdateAttributeGroupEvent::class),
            $this->getResultCallback($expectedAttributeGroup)
        );

        $actualAttributeGroup = $this->service->updateAttributeGroup($attributeGroup, $updateStruct);

        self::assertSame($expectedAttributeGroup, $actualAttributeGroup);
    }

    public function testUpdateAttributeGroupWhenBeforeEventSetsResult(): void
    {
        $attributeGroup = $this->createMock(AttributeGroupInterface::class);
        $updateStruct = new AttributeGroupUpdateStruct();
        $expectedAttributeGroup = $this->createMock(AttributeGroupInterface::class);

        $this->assertInnerServiceIsNotCalled('updateAttributeGroup', [$attributeGroup, $updateStruct]);
        $this->assertBeforeAndAfterEventsAreDispatchedWithOverwrittenResult(
            $this->isValidBeforeUpdateAttributeGroupEvent($attributeGroup, $updateStruct),
            $this->isValidUpdateAttributeGroupEvent($expectedAttributeGroup, $updateStruct),
            $this->getResultCallback($expectedAttributeGroup)
        );

        $actualAttributeGroup = $this->service->updateAttributeGroup($attributeGroup, $updateStruct);

        self::assertSame($expectedAttributeGroup, $actualAttributeGroup);
    }

    private function isValidBeforeCreateAttributeGroupEvent(
        AttributeGroupCreateStruct $expectedCreateStruct
    ): Constraint {
        $callback = static fn (BeforeCreateAttributeGroupEvent $event): bool => [
            $event->getCreateStruct(),
        ] === [$expectedCreateStruct];

        return $this->isValidEvent(BeforeCreateAttributeGroupEvent::class, $callback);
    }

    private function isValidBeforeDeleteAttributeGroupEvent(
        AttributeGroupInterface $expectedAttributeGroup
    ): Constraint {
        $callback = static fn (BeforeDeleteAttributeGroupEvent $event): bool => [
            $event->getAttributeGroup(),
        ] === [$expectedAttributeGroup];

        return $this->isValidEvent(BeforeDeleteAttributeGroupEvent::class, $callback);
    }

    private function isValidBeforeUpdateAttributeGroupEvent(
        AttributeGroupInterface $expectedAttributeGroup,
        AttributeGroupUpdateStruct $expectedUpdateStruct
    ): Constraint {
        $callback = static fn (BeforeUpdateAttributeGroupEvent $event): bool => [
            $event->getAttributeGroup(),
            $event->getUpdateStruct(),
        ] === [$expectedAttributeGroup, $expectedUpdateStruct];

        return $this->isValidEvent(BeforeUpdateAttributeGroupEvent::class, $callback);
    }

    private function isValidCreateAttributeGroupEvent(
        AttributeGroupCreateStruct $expectedCreateStruct,
        AttributeGroupInterface $expectedAttributeGroup
    ): Constraint {
        $callback = static fn (CreateAttributeGroupEvent $event): bool => [
            $event->getCreateStruct(),
            $event->getAttributeGroup(),
        ] === [$expectedCreateStruct, $expectedAttributeGroup];

        return $this->isValidEvent(CreateAttributeGroupEvent::class, $callback);
    }

    private function isValidDeleteAttributeGroupEvent(
        AttributeGroupInterface $expectedAttributeGroup
    ): Constraint {
        $callback = static fn (DeleteAttributeGroupEvent $event): bool => [
            $event->getAttributeGroup(),
        ] === [$expectedAttributeGroup];

        return $this->isValidEvent(DeleteAttributeGroupEvent::class, $callback);
    }

    private function isValidUpdateAttributeGroupEvent(
        AttributeGroupInterface $expectedAttributeGroup,
        AttributeGroupUpdateStruct $expectedUpdateStruct
    ): Constraint {
        $callback = static fn (UpdateAttributeGroupEvent $event): bool => [
            $event->getAttributeGroup(),
            $event->getUpdateStruct(),
        ] == [$expectedAttributeGroup, $expectedUpdateStruct];

        return $this->isValidEvent(UpdateAttributeGroupEvent::class, $callback);
    }

    /**
     * @return callable(\Ibexa\Contracts\Core\Repository\Event\BeforeEvent): void
     */
    private function getResultCallback(AttributeGroupInterface $expectedAttributeGroup): callable
    {
        return static function (Event $event) use ($expectedAttributeGroup): void {
            if ($event instanceof BeforeCreateAttributeGroupEvent ||
                $event instanceof BeforeUpdateAttributeGroupEvent) {
                $event->setResultAttributeGroup($expectedAttributeGroup);
            }
        };
    }
}
