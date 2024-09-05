<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Local\Event;

use Ibexa\Contracts\ProductCatalog\Local\Events\BeforeCreateAttributeDefinitionEvent;
use Ibexa\Contracts\ProductCatalog\Local\Events\BeforeDeleteAttributeDefinitionEvent;
use Ibexa\Contracts\ProductCatalog\Local\Events\BeforeUpdateAttributeDefinitionEvent;
use Ibexa\Contracts\ProductCatalog\Local\Events\CreateAttributeDefinitionEvent;
use Ibexa\Contracts\ProductCatalog\Local\Events\DeleteAttributeDefinitionEvent;
use Ibexa\Contracts\ProductCatalog\Local\Events\UpdateAttributeDefinitionEvent;
use Ibexa\Contracts\ProductCatalog\Local\LocalAttributeDefinitionServiceInterface;
use Ibexa\Contracts\ProductCatalog\Local\Values\AttributeDefinition\AttributeDefinitionCreateStruct;
use Ibexa\Contracts\ProductCatalog\Local\Values\AttributeDefinition\AttributeDefinitionUpdateStruct;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface;
use Ibexa\ProductCatalog\Local\Repository\Event\AttributeDefinitionService;
use PHPUnit\Framework\Constraint\Constraint;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * @template-extends \Ibexa\Tests\ProductCatalog\Local\Event\AbstractEventServiceTest<
 *    \Ibexa\Contracts\ProductCatalog\Local\LocalAttributeDefinitionServiceInterface
 * >
 */
final class AttributeDefinitionServiceTest extends AbstractEventServiceTest
{
    private const EXAMPLE_IDENTIFIER = 'foo';

    private AttributeDefinitionService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new AttributeDefinitionService($this->innerService, $this->eventDispatcher);
    }

    protected function getInnerServiceClass(): string
    {
        return LocalAttributeDefinitionServiceInterface::class;
    }

    public function testCreateAttributeDefinitionDispatchEvents(): void
    {
        $createStruct = new AttributeDefinitionCreateStruct(self::EXAMPLE_IDENTIFIER);
        $expectedAttributeDefinition = $this->createMock(AttributeDefinitionInterface::class);

        $this->assertInnerServiceIsCalled('createAttributeDefinition', [$createStruct], $expectedAttributeDefinition);
        $this->assertBeforeAndAfterEventsAreDispatched(
            $this->isValidBeforeCreateAttributeDefinitionEvent($createStruct),
            $this->isValidCreateAttributeDefinitionEvent($createStruct, $expectedAttributeDefinition)
        );

        $actualAttributeGroup = $this->service->createAttributeDefinition($createStruct);

        self::assertSame($expectedAttributeDefinition, $actualAttributeGroup);
    }

    public function testCreateAttributeDefinitionWhenBeforeEventStoppedPropagation(): void
    {
        $createStruct = new AttributeDefinitionCreateStruct(self::EXAMPLE_IDENTIFIER);
        $expectedAttributeDefinition = $this->createMock(AttributeDefinitionInterface::class);

        $this->assertInnerServiceIsNotCalled('createAttributeDefinition');
        $this->assertBeforeEventIsDispatchedAndStopsPropagation(
            $this->isValidBeforeCreateAttributeDefinitionEvent($createStruct),
            $this->getResultCallback($expectedAttributeDefinition)
        );

        $actualAttributeDefinition = $this->service->createAttributeDefinition($createStruct);

        self::assertSame($expectedAttributeDefinition, $actualAttributeDefinition);
    }

    public function testCreateAttributeDefinitionWhenBeforeEventSetsResult(): void
    {
        $createStruct = new AttributeDefinitionCreateStruct(self::EXAMPLE_IDENTIFIER);
        $expectedAttributeDefinition = $this->createMock(AttributeDefinitionInterface::class);

        $this->assertInnerServiceIsNotCalled('createAttributeDefinition');
        $this->assertBeforeAndAfterEventsAreDispatchedWithOverwrittenResult(
            $this->isValidBeforeCreateAttributeDefinitionEvent($createStruct),
            $this->isValidCreateAttributeDefinitionEvent($createStruct, $expectedAttributeDefinition),
            $this->getResultCallback($expectedAttributeDefinition)
        );

        $actualAttributeDefinition = $this->service->createAttributeDefinition($createStruct);

        self::assertSame($expectedAttributeDefinition, $actualAttributeDefinition);
    }

    public function testDeleteAttributeDefinitionDispatchEvents(): void
    {
        $attributeDefinition = $this->createMock(AttributeDefinitionInterface::class);

        $this->assertInnerServiceIsCalled('deleteAttributeDefinition', [$attributeDefinition]);
        $this->assertBeforeAndAfterEventsAreDispatched(
            $this->isValidBeforeDeleteAttributeDefinitionEvent($attributeDefinition),
            $this->isValidDeleteAttributeDefinitionEvent($attributeDefinition)
        );

        $this->service->deleteAttributeDefinition($attributeDefinition);
    }

    public function testDeleteAttributeDefinitionWhenBeforeEventStoppedPropagation(): void
    {
        $attributeDefinition = $this->createMock(AttributeDefinitionInterface::class);

        $this->assertInnerServiceIsNotCalled('deleteAttributeDefinition', [$attributeDefinition]);
        $this->assertBeforeEventIsDispatchedAndStopsPropagation(
            self::isInstanceOf(BeforeDeleteAttributeDefinitionEvent::class),
        );

        $this->service->deleteAttributeDefinition($attributeDefinition);
    }

    public function testUpdateAttributeDefinitionDispatchEvents(): void
    {
        $attributeDefinition = $this->createMock(AttributeDefinitionInterface::class);
        $updateStruct = new AttributeDefinitionUpdateStruct();
        $expectedAttributeDefinition = $this->createMock(AttributeDefinitionInterface::class);

        $this->assertInnerServiceIsCalled(
            'updateAttributeDefinition',
            [$attributeDefinition, $updateStruct],
            $expectedAttributeDefinition
        );

        $this->assertBeforeAndAfterEventsAreDispatched(
            $this->isValidBeforeUpdateAttributeDefinitionEvent($attributeDefinition, $updateStruct),
            $this->isValidUpdateAttributeDefinitionEvent($expectedAttributeDefinition, $updateStruct)
        );

        $actualAttributeDefinition = $this->service->updateAttributeDefinition($attributeDefinition, $updateStruct);

        self::assertSame($expectedAttributeDefinition, $actualAttributeDefinition);
    }

    public function testUpdateAttributeDefinitionWhenBeforeEventStoppedPropagation(): void
    {
        $attributeDefinition = $this->createMock(AttributeDefinitionInterface::class);
        $updateStruct = new AttributeDefinitionUpdateStruct();
        $expectedAttributeDefinition = $this->createMock(AttributeDefinitionInterface::class);

        $this->assertInnerServiceIsNotCalled('updateAttributeDefinition', [$attributeDefinition, $updateStruct]);
        $this->assertBeforeEventIsDispatchedAndStopsPropagation(
            self::isInstanceOf(BeforeUpdateAttributeDefinitionEvent::class),
            $this->getResultCallback($expectedAttributeDefinition)
        );

        $actualAttributeDefinition = $this->service->updateAttributeDefinition($attributeDefinition, $updateStruct);

        self::assertSame($expectedAttributeDefinition, $actualAttributeDefinition);
    }

    public function testUpdateAttributeGroupWhenBeforeEventSetsResult(): void
    {
        $attributeDefinition = $this->createMock(AttributeDefinitionInterface::class);
        $updateStruct = new AttributeDefinitionUpdateStruct();
        $expectedAttributeDefinition = $this->createMock(AttributeDefinitionInterface::class);

        $this->assertInnerServiceIsNotCalled('updateAttributeDefinition', [$attributeDefinition, $updateStruct]);
        $this->assertBeforeAndAfterEventsAreDispatchedWithOverwrittenResult(
            $this->isValidBeforeUpdateAttributeDefinitionEvent($attributeDefinition, $updateStruct),
            $this->isValidUpdateAttributeDefinitionEvent($expectedAttributeDefinition, $updateStruct),
            $this->getResultCallback($expectedAttributeDefinition)
        );

        $actualAttributeDefinition = $this->service->updateAttributeDefinition($attributeDefinition, $updateStruct);

        self::assertSame($expectedAttributeDefinition, $actualAttributeDefinition);
    }

    private function isValidBeforeCreateAttributeDefinitionEvent(
        AttributeDefinitionCreateStruct $expectedCreateStruct
    ): Constraint {
        $callback = static fn (BeforeCreateAttributeDefinitionEvent $event): bool => [
            $event->getCreateStruct(),
        ] === [$expectedCreateStruct];

        return $this->isValidEvent(BeforeCreateAttributeDefinitionEvent::class, $callback);
    }

    private function isValidCreateAttributeDefinitionEvent(
        AttributeDefinitionCreateStruct $expectedCreateStruct,
        AttributeDefinitionInterface $expectedAttributeDefinition
    ): Constraint {
        $callback = static fn (CreateAttributeDefinitionEvent $event): bool => [
            $event->getCreateStruct(),
            $event->getAttributeDefinition(),
        ] === [$expectedCreateStruct, $expectedAttributeDefinition];

        return $this->isValidEvent(CreateAttributeDefinitionEvent::class, $callback);
    }

    private function isValidBeforeDeleteAttributeDefinitionEvent(
        AttributeDefinitionInterface $expectedAttributeDefinition
    ): Constraint {
        $callback = static fn (BeforeDeleteAttributeDefinitionEvent $event): bool => [
            $event->getAttributeDefinition(),
        ] === [$expectedAttributeDefinition];

        return $this->isValidEvent(BeforeDeleteAttributeDefinitionEvent::class, $callback);
    }

    private function isValidDeleteAttributeDefinitionEvent(
        AttributeDefinitionInterface $expectedAttributeDefinition
    ): Constraint {
        $callback = static fn (DeleteAttributeDefinitionEvent $event): bool => [
            $event->getAttributeDefinition(),
        ] === [$expectedAttributeDefinition];

        return $this->isValidEvent(DeleteAttributeDefinitionEvent::class, $callback);
    }

    private function isValidBeforeUpdateAttributeDefinitionEvent(
        AttributeDefinitionInterface $expectedAttributeDefinition,
        AttributeDefinitionUpdateStruct $expectedUpdateStruct
    ): Constraint {
        $callback = static fn (BeforeUpdateAttributeDefinitionEvent $event): bool => [
            $event->getAttributeDefinition(),
            $event->getUpdateStruct(),
        ] === [$expectedAttributeDefinition, $expectedUpdateStruct];

        return $this->isValidEvent(BeforeUpdateAttributeDefinitionEvent::class, $callback);
    }

    private function isValidUpdateAttributeDefinitionEvent(
        AttributeDefinitionInterface $expectedAttributeDefinition,
        AttributeDefinitionUpdateStruct $expectedUpdateStruct
    ): Constraint {
        $callback = static fn (UpdateAttributeDefinitionEvent $event): bool => [
            $event->getAttributeDefinition(),
            $event->getUpdateStruct(),
        ] === [$expectedAttributeDefinition, $expectedUpdateStruct];

        return $this->isValidEvent(UpdateAttributeDefinitionEvent::class, $callback);
    }

    /**
     * @return callable(\Ibexa\Contracts\Core\Repository\Event\BeforeEvent): void
     */
    private function getResultCallback(AttributeDefinitionInterface $attributeDefinition): callable
    {
        return static function (Event $event) use ($attributeDefinition): void {
            if ($event instanceof BeforeCreateAttributeDefinitionEvent ||
                $event instanceof BeforeUpdateAttributeDefinitionEvent) {
                $event->setResultAttributeDefinition($attributeDefinition);
            }
        };
    }
}
