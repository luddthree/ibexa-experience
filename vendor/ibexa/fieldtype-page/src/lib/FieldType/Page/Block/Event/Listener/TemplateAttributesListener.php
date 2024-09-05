<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\FieldType\Page\Block\Event\Listener;

use Ibexa\FieldTypePage\FieldType\Page\Block\Renderer\BlockRenderEvents;
use Ibexa\FieldTypePage\FieldType\Page\Block\Renderer\Event\PreRenderEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class TemplateAttributesListener implements EventSubscriberInterface
{
    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [
            BlockRenderEvents::GLOBAL_BLOCK_RENDER_PRE => 'onBlockPreRender',
        ];
    }

    /**
     * @param \Ibexa\FieldTypePage\FieldType\Page\Block\Renderer\Event\PreRenderEvent $event
     */
    public function onBlockPreRender(PreRenderEvent $event)
    {
        $blockValue = $event->getBlockValue();
        $renderRequest = $event->getRenderRequest();

        $parameters = $renderRequest->getParameters();

        foreach ($blockValue->getAttributes() as $attribute) {
            $parameters[$attribute->getName()] = $attribute->getValue();
        }

        $parameters['block_id'] = $blockValue->getId();
        $parameters['block_name'] = $blockValue->getName();
        $parameters['block_class'] = $blockValue->getClass();
        $parameters['block_style'] = $blockValue->getCompiled();

        $renderRequest->setParameters($parameters);
    }
}

class_alias(TemplateAttributesListener::class, 'EzSystems\EzPlatformPageFieldType\FieldType\Page\Block\Event\Listener\TemplateAttributesListener');
