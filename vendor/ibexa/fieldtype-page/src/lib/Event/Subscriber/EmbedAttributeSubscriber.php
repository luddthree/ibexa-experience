<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\Event\Subscriber;

use Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\BlockDefinition;
use Ibexa\FieldTypePage\Event\PageEvents;
use Ibexa\FieldTypePage\Event\PageFromPersistenceEvent;
use Ibexa\FieldTypePage\FieldType\Page\Block\Definition\BlockDefinitionFactoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class EmbedAttributeSubscriber implements EventSubscriberInterface
{
    /** @var \Ibexa\FieldTypePage\FieldType\Page\Block\Definition\BlockDefinitionFactoryInterface */
    private $blockDefinitionFactory;

    /** @var string[] */
    private $attributeTypes;

    public function __construct(
        BlockDefinitionFactoryInterface $blockDefinitionFactory,
        array $attributeTypes
    ) {
        $this->blockDefinitionFactory = $blockDefinitionFactory;
        $this->attributeTypes = $attributeTypes;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            PageEvents::PERSISTENCE_FROM => 'onPageLoadFromPersistence',
        ];
    }

    public function onPageLoadFromPersistence(PageFromPersistenceEvent $event): void
    {
        $page = $event->getValue()->getPage();

        $visitedBlockDefinitions = [];
        foreach ($page->getBlockIterator() as $block) {
            $type = $block->getType();
            if (!$this->isBlockTypeValid($type, $visitedBlockDefinitions)) {
                continue;
            }

            $blockDefinition = $this->getBlockDefinition($type, $visitedBlockDefinitions);
            $visitedBlockDefinitions[$type] = $blockDefinition;

            foreach ($blockDefinition->getAttributes() as $attributeDefinition) {
                if (!in_array($attributeDefinition->getType(), $this->attributeTypes)) {
                    continue;
                }

                $attribute = $block->getAttribute($attributeDefinition->getIdentifier());
                if ($attribute !== null && is_string($attribute->getValue())) {
                    $attribute->setValue((int)$attribute->getValue());
                }
            }
        }
    }

    private function isBlockTypeValid(string $type, array $visitedBlockDefinitions): bool
    {
        return isset($visitedBlockDefinitions[$type]) || $this->blockDefinitionFactory->hasBlockDefinition($type);
    }

    private function getBlockDefinition(string $type, array $visitedBlockDefinitions): BlockDefinition
    {
        return $visitedBlockDefinitions[$type] ?? $this->blockDefinitionFactory->getBlockDefinition($type);
    }
}

class_alias(EmbedAttributeSubscriber::class, 'EzSystems\EzPlatformPageFieldType\Event\Subscriber\EmbedAttributeSubscriber');
