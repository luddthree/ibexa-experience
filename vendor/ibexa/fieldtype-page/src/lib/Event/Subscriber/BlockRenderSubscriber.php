<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\Event\Subscriber;

use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Zone;
use Ibexa\FieldTypePage\FieldType\Page\Block\Renderer\BlockRenderEvents;
use Ibexa\FieldTypePage\FieldType\Page\Block\Renderer\Event\PreRenderEvent;
use Ibexa\FieldTypePage\FieldType\Page\Block\Renderer\Twig\TwigRenderRequest;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class BlockRenderSubscriber implements EventSubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            BlockRenderEvents::GLOBAL_BLOCK_RENDER_PRE => ['onBlockPreRender', 0],
        ];
    }

    /**
     * @param \Ibexa\FieldTypePage\FieldType\Page\Block\Renderer\Event\PreRenderEvent $event
     */
    public function onBlockPreRender(PreRenderEvent $event): void
    {
        $blockContext = $event->getBlockContext();
        $renderRequest = $event->getRenderRequest();

        if (!$renderRequest instanceof TwigRenderRequest) {
            return;
        }

        $parameters = array_merge(
            $renderRequest->getParameters(),
            [
                'block_context' => $blockContext,
                'zone' => $this->getZone($event),
            ]
        );

        $renderRequest->setParameters($parameters);
    }

    /**
     * @param \Ibexa\FieldTypePage\FieldType\Page\Block\Renderer\Event\PreRenderEvent $event
     *
     * @return \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Zone|null
     */
    private function getZone(PreRenderEvent $event): ?Zone
    {
        $blockValue = $event->getBlockValue();
        $blockContext = $event->getBlockContext();
        $page = $blockContext->getPage();

        foreach ($page->getZones() as $zone) {
            foreach ($zone->getBlocks() as $block) {
                if ($block->getId() === $blockValue->getId()) {
                    return $zone;
                }
            }
        }

        return null;
    }
}

class_alias(BlockRenderSubscriber::class, 'EzSystems\EzPlatformPageFieldType\Event\Subscriber\BlockRenderSubscriber');
