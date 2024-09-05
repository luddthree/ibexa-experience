<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\Event;

use Ibexa\Contracts\ProductCatalog\Local\Events\BeforeCreateAttributeGroupEvent;
use Ibexa\Contracts\ProductCatalog\Local\Events\BeforeDeleteAttributeGroupEvent;
use Ibexa\Contracts\ProductCatalog\Local\Events\BeforeUpdateAttributeGroupEvent;
use Ibexa\Contracts\ProductCatalog\Local\Events\CreateAttributeGroupEvent;
use Ibexa\Contracts\ProductCatalog\Local\Events\DeleteAttributeGroupEvent;
use Ibexa\Contracts\ProductCatalog\Local\Events\UpdateAttributeGroupEvent;
use Ibexa\Contracts\ProductCatalog\Local\LocalAttributeGroupServiceDecorator;
use Ibexa\Contracts\ProductCatalog\Local\LocalAttributeGroupServiceInterface;
use Ibexa\Contracts\ProductCatalog\Local\Values\AttributeGroup\AttributeGroupCreateStruct;
use Ibexa\Contracts\ProductCatalog\Local\Values\AttributeGroup\AttributeGroupUpdateStruct;
use Ibexa\Contracts\ProductCatalog\Values\AttributeGroupInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class AttributeGroupService extends LocalAttributeGroupServiceDecorator
{
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(
        LocalAttributeGroupServiceInterface $innerService,
        EventDispatcherInterface $eventDispatcher
    ) {
        parent::__construct($innerService);

        $this->eventDispatcher = $eventDispatcher;
    }

    public function createAttributeGroup(AttributeGroupCreateStruct $createStruct): AttributeGroupInterface
    {
        $beforeEvent = new BeforeCreateAttributeGroupEvent($createStruct);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return $beforeEvent->getResultAttributeGroup();
        }

        $attributeGroup = $beforeEvent->hasResultAttributeGroup()
            ? $beforeEvent->getResultAttributeGroup()
            : $this->innerService->createAttributeGroup($createStruct);

        $this->eventDispatcher->dispatch(new CreateAttributeGroupEvent($createStruct, $attributeGroup));

        return $attributeGroup;
    }

    public function deleteAttributeGroup(AttributeGroupInterface $group): void
    {
        $beforeEvent = new BeforeDeleteAttributeGroupEvent($group);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return;
        }

        $this->innerService->deleteAttributeGroup($group);

        $this->eventDispatcher->dispatch(new DeleteAttributeGroupEvent($group));
    }

    public function updateAttributeGroup(
        AttributeGroupInterface $attributeGroup,
        AttributeGroupUpdateStruct $updateStruct
    ): AttributeGroupInterface {
        $beforeEvent = new BeforeUpdateAttributeGroupEvent($attributeGroup, $updateStruct);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return $beforeEvent->getResultAttributeGroup();
        }

        $attributeGroup = $beforeEvent->hasResultAttributeGroup()
            ? $beforeEvent->getResultAttributeGroup()
            : $this->innerService->updateAttributeGroup($attributeGroup, $updateStruct);

        $this->eventDispatcher->dispatch(new UpdateAttributeGroupEvent($attributeGroup, $updateStruct));

        return $attributeGroup;
    }
}
