<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ActivityLog\EventSubscriber\PageBuilder;

use Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\BlockAttributeDefinition;
use Ibexa\FieldTypePage\Event\AttributeSerializationEvent;
use Ibexa\FieldTypePage\Event\PageEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @template T of object
 */
abstract class AbstractChoiceAttributeSerializationSubscriber implements EventSubscriberInterface
{
    protected const SERIALIZATION_SEPARATOR = ',';

    abstract protected function supportsAttribute(BlockAttributeDefinition $attributeDefinition): bool;

    /**
     * @return T|null
     */
    abstract protected function deserializeSingleItem(string $serializedValue): ?object;

    /**
     * @phpstan-param T $value
     */
    abstract protected function serializeSingleItem(object $value): string;

    protected function isMultipleValues(BlockAttributeDefinition $attributeDefinition): bool
    {
        return $attributeDefinition->getOptions()['multiple'] ?? false;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            PageEvents::ATTRIBUTE_SERIALIZATION => ['onAttributeSerialization', 10],
            PageEvents::ATTRIBUTE_DESERIALIZATION => ['onAttributeDeserialization', 10],
        ];
    }

    final public function onAttributeSerialization(AttributeSerializationEvent $event): void
    {
        $attributeDefinition = $event->getAttributeDefinition();
        if ($attributeDefinition === null || !$this->supportsAttribute($attributeDefinition)) {
            return;
        }

        $deserializedValue = $event->getDeserializedValue();
        if ($deserializedValue === null) {
            return;
        }

        if (!is_array($deserializedValue)) {
            $deserializedValue = [$deserializedValue];
        }

        $serializedValue = implode(
            self::SERIALIZATION_SEPARATOR,
            array_map([$this, 'serializeSingleItem'], $deserializedValue),
        );
        $event->setSerializedValue($serializedValue);
        $event->stopPropagation();
    }

    final public function onAttributeDeserialization(AttributeSerializationEvent $event): void
    {
        $serializedValue = $event->getSerializedValue();
        if (!is_string($serializedValue)) {
            return;
        }

        $attributeDefinition = $event->getAttributeDefinition();
        if ($attributeDefinition === null || !$this->supportsAttribute($attributeDefinition)) {
            return;
        }

        if (!$this->isMultipleValues($attributeDefinition)) {
            $object = $this->deserializeSingleItem($serializedValue);
            if ($object !== null) {
                $event->setDeserializedValue($object);
            }

            $event->stopPropagation();

            return;
        }

        $serializedValues = explode(self::SERIALIZATION_SEPARATOR, $serializedValue);

        $matches = [];
        foreach ($serializedValues as $value) {
            $object = $this->deserializeSingleItem($value);
            if ($object !== null) {
                $matches[] = $object;
            }
        }

        $event->setDeserializedValue($matches);
        $event->stopPropagation();
    }
}
