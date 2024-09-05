<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\FieldType\Page\Block\Event\Listener;

use Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\BlockDefinitionEvents;
use Ibexa\FieldTypePage\FieldType\Page\Block\Attribute\ValueConverter;
use Ibexa\FieldTypePage\FieldType\Page\Block\Definition\Event\BlockAttributeDefinitionEvent;
use Ibexa\FieldTypePage\FieldType\Page\Block\Renderer\BlockRenderEvents;
use Ibexa\FieldTypePage\FieldType\Page\Block\Renderer\Event\PreRenderEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CollectionBlockListener implements EventSubscriberInterface
{
    /** @var \Ibexa\FieldTypePage\FieldType\Page\Block\Attribute\ValueConverter\LocationList */
    private $valueConverter;

    /**
     * @param \Ibexa\FieldTypePage\FieldType\Page\Block\Attribute\ValueConverter\LocationList $valueConverter
     */
    public function __construct(ValueConverter\LocationList $valueConverter)
    {
        $this->valueConverter = $valueConverter;
    }

    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents(): array
    {
        return [
            BlockRenderEvents::getBlockPreRenderEventName('collection') => 'onBlockPreRender',
            BlockDefinitionEvents::getBlockAttributeDefinitionEventName('collection', 'locationlist') => 'onLocationListAttributeDefinition',
        ];
    }

    /**
     * @param \Ibexa\FieldTypePage\FieldType\Page\Block\Renderer\Event\PreRenderEvent $event
     */
    public function onBlockPreRender(PreRenderEvent $event): void
    {
        $blockValue = $event->getBlockValue();
        $renderRequest = $event->getRenderRequest();

        $parameters = $renderRequest->getParameters();

        $locationListAttribute = $blockValue->getAttribute('locationlist');
        $locations = $this->valueConverter->fromStorageValue($locationListAttribute->getValue());

        /** @todo Mapping to ['locationId'] has been kept just for BC, should be removed */
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Location $location */
        $parameters['locationlist'] = array_map(static function ($location) {
            return ['locationId' => $location->id];
        }, $locations);

        $renderRequest->setParameters($parameters);
    }

    /**
     * @param \Ibexa\FieldTypePage\FieldType\Page\Block\Definition\Event\BlockAttributeDefinitionEvent $event
     */
    public function onLocationListAttributeDefinition(BlockAttributeDefinitionEvent $event): void
    {
        $definition = $event->getDefinition();
        $configuration = $event->getConfiguration();

        $options = $definition->getOptions();
        $options['match'] = $this->getViewMatchConfiguration($configuration);

        $definition->setOptions($options);
    }

    /**
     * @param array $configuration
     *
     * @return array
     */
    private function getViewMatchConfiguration(array $configuration): array
    {
        $list = [];

        foreach ($configuration['views'] as $viewName => $viewConfig) {
            if (!isset($viewConfig['options'])
                || !isset($viewConfig['options']['match'])
                || empty($viewConfig['options']['match'])
            ) {
                $list[$viewName] = [];
                continue;
            }
            $list[$viewName] = $viewConfig['options']['match'];
        }

        return $list;
    }
}

class_alias(CollectionBlockListener::class, 'EzSystems\EzPlatformPageFieldType\FieldType\Page\Block\Event\Listener\CollectionBlockListener');
