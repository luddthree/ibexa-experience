<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\FieldType\Page\Block\Event\Listener;

use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue;
use Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\ReactBlockDefinition;
use Ibexa\FieldTypePage\FieldType\Page\Block\Definition\BlockDefinitionFactoryInterface;
use Ibexa\FieldTypePage\FieldType\Page\Block\Renderer\BlockRenderEvents;
use Ibexa\FieldTypePage\FieldType\Page\Block\Renderer\Event\PreRenderEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class ReactBlockListener implements EventSubscriberInterface
{
    private BlockDefinitionFactoryInterface $blockDefinitionFactory;

    public function __construct(BlockDefinitionFactoryInterface $blockDefinitionFactory)
    {
        $this->blockDefinitionFactory = $blockDefinitionFactory;
    }

    /**
     * @return array<string, array<string>|string>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            BlockRenderEvents::GLOBAL_BLOCK_RENDER_PRE => 'onBlockPreRender',
        ];
    }

    public function onBlockPreRender(PreRenderEvent $event): void
    {
        $blockValue = $event->getBlockValue();
        $renderRequest = $event->getRenderRequest();

        /** @var \Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\ReactBlockDefinition $blockDefinition */
        $blockDefinition = $this->blockDefinitionFactory->getBlockDefinition($blockValue->getType());

        if (!$blockDefinition instanceof ReactBlockDefinition) {
            return;
        }

        $parameters = $renderRequest->getParameters();
        $parameters['component'] = $blockDefinition->getComponent();

        $parameters['props'] = [
            'attributes' => $this->getAttributes($blockValue),
            'name' => $blockValue->getName(),
            'type' => $blockValue->getType(),
        ];

        $renderRequest->setParameters($parameters);
    }

    /**
     * @return array<string, mixed>
     */
    private function getAttributes(BlockValue $blockValue): array
    {
        $attributes = [];
        foreach ($blockValue->getAttributes() as $attribute) {
            $attributes[$attribute->getName()] = $attribute->getValue();
        }

        return $attributes;
    }
}
