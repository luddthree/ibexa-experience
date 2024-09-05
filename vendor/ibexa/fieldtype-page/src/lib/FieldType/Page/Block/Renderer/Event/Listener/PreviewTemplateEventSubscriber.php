<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\FieldType\Page\Block\Renderer\Event\Listener;

use Ibexa\FieldTypePage\FieldType\Page\Block\Renderer\BlockRenderEvents;
use Ibexa\FieldTypePage\FieldType\Page\Block\Renderer\Event\PostRenderEvent;
use Ibexa\PageBuilder\Block\Context\ContentCreateBlockContext;
use Ibexa\PageBuilder\Block\Context\ContentEditBlockContext;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Twig\Environment;

/**
 * @deprecated Since 1.1.0. Will be removed in 2.0.
 */
class PreviewTemplateEventSubscriber implements EventSubscriberInterface
{
    /** @var \Twig\Environment */
    private $templating;

    /**
     * @param \Twig\Environment $templating
     */
    public function __construct(Environment $templating)
    {
        $this->templating = $templating;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            BlockRenderEvents::GLOBAL_BLOCK_RENDER_POST => 'onBlockPostRender',
        ];
    }

    /**
     * @param \Ibexa\FieldTypePage\FieldType\Page\Block\Renderer\Event\PostRenderEvent $event
     *
     * @throws \RuntimeException
     */
    public function onBlockPostRender(PostRenderEvent $event)
    {
        if (
            !\in_array(
                \get_class($event->getBlockContext()),
                [ContentCreateBlockContext::class, ContentEditBlockContext::class],
                true
            )
        ) {
            return;
        }

        $blockContent = $event->getRenderedBlock();

        $event->setRenderedBlock(
            $this->templating->render(
                '@IbexaFieldTypePage/block_preview.html.twig',
                [
                    'block_value' => $event->getBlockValue(),
                    'rendered_block_content' => $blockContent,
                ]
            )
        );
    }
}

class_alias(PreviewTemplateEventSubscriber::class, 'EzSystems\EzPlatformPageFieldType\FieldType\Page\Block\Renderer\Event\Listener\PreviewTemplateEventSubscriber');
