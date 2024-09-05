<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\Event\Subscriber;

use Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\BlockDefinition;
use Ibexa\FieldTypePage\Event\BlockRelationEvents;
use Ibexa\FieldTypePage\Event\CollectBlockRelationsEvent;
use Ibexa\FieldTypePage\FieldType\Page\Block\Definition\BlockDefinitionFactoryInterface;
use Ibexa\FieldTypePage\FieldType\Page\Block\Relation\BlockAttributeTypeRelationExtractorRegistry;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CollectRelationsSubscriber implements EventSubscriberInterface
{
    /** @var \Ibexa\FieldTypePage\FieldType\Page\Block\Relation\BlockAttributeTypeRelationExtractorRegistry */
    private $extractorRegistry;

    /** @var \Ibexa\FieldTypePage\FieldType\Page\Block\Definition\BlockDefinitionFactoryInterface */
    private $blockDefinitionFactory;

    public function __construct(
        BlockAttributeTypeRelationExtractorRegistry $extractorRegistry,
        BlockDefinitionFactoryInterface $blockDefinitionFactory
    ) {
        $this->extractorRegistry = $extractorRegistry;
        $this->blockDefinitionFactory = $blockDefinitionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            BlockRelationEvents::COLLECT_BLOCK_RELATIONS => ['onCollectBlockRelations', 20],
        ];
    }

    /**
     * @param \Ibexa\FieldTypePage\Event\CollectBlockRelationsEvent $event
     */
    public function onCollectBlockRelations(CollectBlockRelationsEvent $event): void
    {
        $fieldValue = $event->getFieldValue();
        $blockValue = $event->getBlockValue();
        $relations = $event->getRelations();
        $page = $fieldValue->getPage();
        $newRelations = [];

        foreach ($blockValue->getAttributes() as $attribute) {
            try {
                $blockDefinition = $this->blockDefinitionFactory->getBlockDefinition($blockValue->getType());
            } catch (\Exception $e) {
                continue;
            }

            $attributeDefinition = $this->getAttributeDefinitionFromBlock($blockDefinition, $attribute->getName());

            if (null === $attributeDefinition) {
                continue;
            }

            $extractors = $this->extractorRegistry->getApplicableExtractors(
                $page,
                $blockValue,
                $attributeDefinition,
                $attribute
            );

            foreach ($extractors as $extractor) {
                $newRelations[] = $extractor->extract($page, $blockValue, $attributeDefinition, $attribute);
            }
        }

        if (\count($newRelations) === 0) {
            return;
        }

        $event->setRelations(
            array_merge($relations, array_merge(...$newRelations))
        );
    }

    /**
     * @param \Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\BlockDefinition $blockDefinition
     * @param string $name
     *
     * @return \Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\BlockAttributeDefinition|null
     */
    private function getAttributeDefinitionFromBlock(BlockDefinition $blockDefinition, string $name)
    {
        foreach ($blockDefinition->getAttributes() as $attributeDefinition) {
            if ($attributeDefinition->getIdentifier() === $name) {
                return $attributeDefinition;
            }
        }

        return null;
    }
}

class_alias(CollectRelationsSubscriber::class, 'EzSystems\EzPlatformPageFieldType\Event\Subscriber\CollectRelationsSubscriber');
