<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Dashboard\EventSubscriber\PageBuilder;

use Ibexa\Dashboard\News\FeedException;
use Ibexa\Dashboard\News\FeedInterface;
use Ibexa\FieldTypePage\FieldType\Page\Block\Renderer\BlockRenderEvents;
use Ibexa\FieldTypePage\FieldType\Page\Block\Renderer\Event\PreRenderEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class IbexaNewsBlockSubscriber implements EventSubscriberInterface
{
    private const BLOCK_IDENTIFIER = 'ibexa_news';

    private FeedInterface $feed;

    private string $url;

    public function __construct(
        FeedInterface $feed,
        string $url
    ) {
        $this->feed = $feed;
        $this->url = $url;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            BlockRenderEvents::getBlockPreRenderEventName(self::BLOCK_IDENTIFIER) => 'onBlockPreRender',
        ];
    }

    public function onBlockPreRender(PreRenderEvent $event): void
    {
        $ibexaNews = [];
        /** @var \Ibexa\FieldTypePage\FieldType\Page\Block\Renderer\Twig\TwigRenderRequest $request */
        $request = $event->getRenderRequest();
        $request->addParameter('block_name', $event->getBlockValue()->getName());

        try {
            $ibexaNews = $this->feed->fetch(
                $this->url,
                (int)$request->getParameters()['limit'],
            );
        } catch (FeedException $e) {
            /** @var \Ibexa\FieldTypePage\FieldType\Page\Block\Renderer\Twig\TwigRenderRequest $renderRequest */
            $renderRequest = $event->getRenderRequest();
            $renderRequest->setTemplate(
                '@ibexadesign/ui/page_builder/blocks/ibexa_news_unable_to_fetch.html.twig'
            );
        }

        $request->addParameter(
            'ibexa_news',
            $ibexaNews
        );
    }
}
