<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\Event\Subscriber;

use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Attribute;
use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue;
use Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\BlockDefinition;
use Ibexa\FieldTypePage\Event\PageEvents;
use Ibexa\FieldTypePage\Event\PageFromPersistenceEvent;
use Ibexa\FieldTypePage\FieldType\Page\Block\Definition\BlockDefinitionFactoryInterface;
use Ibexa\FieldTypePage\Serializer\AttributeSerializationDispatcher;
use Ramsey\Uuid\Uuid;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AddMissingAttributesOnLoadSubscriber implements EventSubscriberInterface
{
    /** @var \Ibexa\FieldTypePage\FieldType\Page\Block\Definition\BlockDefinitionFactoryInterface */
    private $blockDefinitionFactory;

    /** @var \Ibexa\FieldTypePage\Serializer\AttributeSerializationDispatcher */
    private $attributeSerializationDispatcher;

    public function __construct(
        BlockDefinitionFactoryInterface $blockDefinitionFactory,
        AttributeSerializationDispatcher $attributeSerializationDispatcher
    ) {
        $this->blockDefinitionFactory = $blockDefinitionFactory;
        $this->attributeSerializationDispatcher = $attributeSerializationDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            PageEvents::PERSISTENCE_FROM => ['onLoadFromPersistence', 100],
        ];
    }

    /**
     * @param \Ibexa\FieldTypePage\Event\PageFromPersistenceEvent $event
     */
    public function onLoadFromPersistence(PageFromPersistenceEvent $event): void
    {
        $value = $event->getValue();
        $page = $value->getPage();

        // local cache to save roundtrips to redis/fs cache
        $visitedBlockDefinitions = [];
        foreach ($page->getZones() as $zone) {
            foreach ($zone->getBlocks() as $block) {
                $type = $block->getType();

                $blockDefinition = $this->getBlockDefinition($type, $visitedBlockDefinitions);
                $visitedBlockDefinitions[$type] = $blockDefinition;

                $block->setAttributes(
                    array_merge($block->getAttributes(), $this->getMissingAttributes($block, $blockDefinition))
                );
            }
        }

        $event->setValue($value);
    }

    /**
     * @return \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Attribute[]
     */
    private function getMissingAttributes(BlockValue $block, BlockDefinition $blockDefinition): array
    {
        $missingAttributes = [];
        foreach ($blockDefinition->getAttributes() as $attributeDefinition) {
            if ($this->hasAttribute($block, $attributeDefinition->getIdentifier())) {
                continue;
            }

            $deserializedValue = $this->attributeSerializationDispatcher->deserialize(
                $block,
                $attributeDefinition->getIdentifier(),
                $attributeDefinition->getValue()
            );

            $missingAttributes[] = new Attribute(
                Uuid::uuid1()->toString(),
                $attributeDefinition->getIdentifier(),
                $deserializedValue
            );
        }

        return $missingAttributes;
    }

    /**
     * @param \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue $blockValue
     * @param string $attributeIdentifier
     *
     * @return bool
     */
    private function hasAttribute(BlockValue $blockValue, string $attributeIdentifier): bool
    {
        foreach ($blockValue->getAttributes() as $attribute) {
            if ($attribute->getName() === $attributeIdentifier) {
                return true;
            }
        }

        return false;
    }

    private function getBlockDefinition(string $type, array $visitedBlockDefinitions): BlockDefinition
    {
        return $visitedBlockDefinitions[$type] ?? $this->blockDefinitionFactory->getBlockDefinition($type);
    }
}

class_alias(AddMissingAttributesOnLoadSubscriber::class, 'EzSystems\EzPlatformPageFieldType\Event\Subscriber\AddMissingAttributesOnLoadSubscriber');
