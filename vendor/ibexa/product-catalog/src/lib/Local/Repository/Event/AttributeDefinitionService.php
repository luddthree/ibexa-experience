<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\Event;

use Ibexa\Contracts\ProductCatalog\Local\Events\BeforeCreateAttributeDefinitionEvent;
use Ibexa\Contracts\ProductCatalog\Local\Events\BeforeDeleteAttributeDefinitionEvent;
use Ibexa\Contracts\ProductCatalog\Local\Events\BeforeUpdateAttributeDefinitionEvent;
use Ibexa\Contracts\ProductCatalog\Local\Events\CreateAttributeDefinitionEvent;
use Ibexa\Contracts\ProductCatalog\Local\Events\DeleteAttributeDefinitionEvent;
use Ibexa\Contracts\ProductCatalog\Local\Events\UpdateAttributeDefinitionEvent;
use Ibexa\Contracts\ProductCatalog\Local\LocalAttributeDefinitionServiceDecorator;
use Ibexa\Contracts\ProductCatalog\Local\LocalAttributeDefinitionServiceInterface;
use Ibexa\Contracts\ProductCatalog\Local\Values\AttributeDefinition\AttributeDefinitionCreateStruct;
use Ibexa\Contracts\ProductCatalog\Local\Values\AttributeDefinition\AttributeDefinitionUpdateStruct;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class AttributeDefinitionService extends LocalAttributeDefinitionServiceDecorator
{
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(
        LocalAttributeDefinitionServiceInterface $innerService,
        EventDispatcherInterface $eventDispatcher
    ) {
        parent::__construct($innerService);

        $this->eventDispatcher = $eventDispatcher;
    }

    public function createAttributeDefinition(
        AttributeDefinitionCreateStruct $createStruct
    ): AttributeDefinitionInterface {
        $beforeEvent = new BeforeCreateAttributeDefinitionEvent($createStruct);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return $beforeEvent->getResultAttributeDefinition();
        }

        $attributeDefinition = $beforeEvent->hasResultAttributeDefinition()
            ? $beforeEvent->getResultAttributeDefinition()
            : $this->innerService->createAttributeDefinition($createStruct);

        $this->eventDispatcher->dispatch(new CreateAttributeDefinitionEvent($createStruct, $attributeDefinition));

        return $attributeDefinition;
    }

    public function deleteAttributeDefinition(AttributeDefinitionInterface $attributeDefinition): void
    {
        $beforeEvent = new BeforeDeleteAttributeDefinitionEvent($attributeDefinition);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return;
        }

        $this->innerService->deleteAttributeDefinition($attributeDefinition);

        $this->eventDispatcher->dispatch(new DeleteAttributeDefinitionEvent($attributeDefinition));
    }

    public function updateAttributeDefinition(
        AttributeDefinitionInterface $attributeDefinition,
        AttributeDefinitionUpdateStruct $updateStruct
    ): AttributeDefinitionInterface {
        $beforeEvent = new BeforeUpdateAttributeDefinitionEvent($attributeDefinition, $updateStruct);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return $beforeEvent->getResultAttributeDefinition();
        }

        $attributeDefinition = $beforeEvent->hasResultAttributeDefinition()
            ? $beforeEvent->getResultAttributeDefinition()
            : $this->innerService->updateAttributeDefinition($attributeDefinition, $updateStruct);

        $this->eventDispatcher->dispatch(new UpdateAttributeDefinitionEvent($attributeDefinition, $updateStruct));

        return $attributeDefinition;
    }
}
