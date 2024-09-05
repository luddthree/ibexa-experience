<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\FieldType\Page\Block\Definition;

use Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\BlockAttributeDefinition;
use Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\BlockDefinitionEvents;
use Ibexa\FieldTypePage\FieldType\Page\Block\Definition\Event\BlockAttributeDefinitionEvent;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class BlockAttributeFactory implements BlockAttributeFactoryInterface
{
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function create(
        string $identifier,
        string $attributeIdentifier,
        array $attribute,
        array $configuration
    ): BlockAttributeDefinition {
        $blockAttributeDefinition = new BlockAttributeDefinition();
        $blockAttributeDefinition->setIdentifier($attributeIdentifier);
        $blockAttributeDefinition->setName($attribute['name'] ?? $attributeIdentifier);
        $blockAttributeDefinition->setType($attribute['type']);
        $blockAttributeDefinition->setConstraints($attribute['validators'] ?? []);
        $blockAttributeDefinition->setValue($attribute['value'] ?? null);
        $blockAttributeDefinition->setCategory($attribute['category']);
        $blockAttributeDefinition->setOptions($attribute['options'] ?? []);

        $blockAttributeDefinitionEvent = $this->eventDispatcher->dispatch(
            new BlockAttributeDefinitionEvent($blockAttributeDefinition, $configuration),
            BlockDefinitionEvents::getBlockAttributeDefinitionEventName($identifier, $attributeIdentifier)
        );

        return $blockAttributeDefinitionEvent->getDefinition();
    }
}
