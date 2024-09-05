<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\Serializer;

use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Attribute;
use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue;
use Ibexa\FieldTypePage\Event\AttributeSerializationEvent;
use Ibexa\FieldTypePage\Event\PageEvents;
use Ibexa\FieldTypePage\FieldType\Page\Block\Definition\BlockDefinitionFactoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class AttributeSerializationDispatcher
{
    /** @var \Symfony\Component\EventDispatcher\EventDispatcherInterface */
    protected $eventDispatcher;

    private BlockDefinitionFactoryInterface $blockDefinitionFactory;

    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        BlockDefinitionFactoryInterface $blockDefinitionFactory
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->blockDefinitionFactory = $blockDefinitionFactory;
    }

    /**
     * @return mixed|null
     */
    public function serialize(BlockValue $blockValue, Attribute $attribute)
    {
        $attributeIdentifier = $attribute->getName();
        $blockDefinition = $this->blockDefinitionFactory->getBlockDefinition($blockValue->getType());
        $attributeDefinition = $blockDefinition->getAttribute($attributeIdentifier);

        $event = new AttributeSerializationEvent($blockValue, $attributeIdentifier, $attributeDefinition);
        $event->setDeserializedValue($attribute->getValue());

        $this->eventDispatcher->dispatch($event, PageEvents::ATTRIBUTE_SERIALIZATION);
        $this->eventDispatcher->dispatch($event, PageEvents::getAttributeSerializationEventName($blockValue->getType()));

        return $event->getSerializedValue();
    }

    /**
     * @param \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue $blockValue
     * @param string $attributeIdentifier
     * @param mixed|null $serializedValue
     *
     * @return mixed|null
     */
    public function deserialize(BlockValue $blockValue, string $attributeIdentifier, $serializedValue)
    {
        $blockDefinition = $this->blockDefinitionFactory->getBlockDefinition($blockValue->getType());
        $attributeDefinition = $blockDefinition->getAttribute($attributeIdentifier);

        $event = new AttributeSerializationEvent($blockValue, $attributeIdentifier, $attributeDefinition);
        $event->setSerializedValue($serializedValue);

        $this->eventDispatcher->dispatch($event, PageEvents::ATTRIBUTE_DESERIALIZATION);
        $this->eventDispatcher->dispatch($event, PageEvents::getAttributeDeserializationEventName($blockValue->getType()));

        return $event->getDeserializedValue();
    }
}

class_alias(AttributeSerializationDispatcher::class, 'EzSystems\EzPlatformPageFieldType\Serializer\AttributeSerializationDispatcher');
