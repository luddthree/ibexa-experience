<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\Event\Subscriber;

use Ibexa\FieldTypePage\Event\AttributeSerializationEvent;
use Ibexa\FieldTypePage\Event\PageEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AttributeSerializationSubscriber implements EventSubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            PageEvents::ATTRIBUTE_SERIALIZATION => ['onAttributeSerialization', 0],
            PageEvents::ATTRIBUTE_DESERIALIZATION => ['onAttributeDeserialization', 0],
        ];
    }

    /**
     * @param \Ibexa\FieldTypePage\Event\AttributeSerializationEvent $event
     */
    public function onAttributeSerialization(AttributeSerializationEvent $event): void
    {
        $deserializedValue = $event->getDeserializedValue();

        if ($deserializedValue === null || !is_scalar($deserializedValue)) {
            return;
        }

        $event->setSerializedValue($deserializedValue);
    }

    /**
     * @param \Ibexa\FieldTypePage\Event\AttributeSerializationEvent $event
     */
    public function onAttributeDeserialization(AttributeSerializationEvent $event): void
    {
        $serializedValue = $event->getSerializedValue();

        if ($serializedValue === null || !is_scalar($serializedValue)) {
            return;
        }

        $event->setDeserializedValue($serializedValue);
    }
}

class_alias(AttributeSerializationSubscriber::class, 'EzSystems\EzPlatformPageFieldType\Event\Subscriber\AttributeSerializationSubscriber');
