<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\FieldType\Page\Block\Event\Listener;

use ezcBaseFileNotFoundException;
use ezcFeed;
use ezcFeedParseErrorException;
use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\FieldTypePage\Exception\BlockRenderException;
use Ibexa\FieldTypePage\FieldType\Page\Block\Renderer\BlockRenderEvents;
use Ibexa\FieldTypePage\FieldType\Page\Block\Renderer\Event\PreRenderEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class RssBlockListener implements EventSubscriberInterface
{
    /** @var \Ibexa\Contracts\Core\Repository\ContentService */
    private $contentService;

    /**
     * RssBlockListener constructor.
     *
     * @param \Ibexa\Contracts\Core\Repository\ContentService $contentService
     */
    public function __construct(ContentService $contentService)
    {
        $this->contentService = $contentService;
    }

    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [
            BlockRenderEvents::getBlockPreRenderEventName('rss') => 'onBlockPreRender',
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

        $limitAttribute = $blockValue->getAttribute('limit');
        $offsetAttribute = $blockValue->getAttribute('offset');
        $urlAttribute = $blockValue->getAttribute('url');

        try {
            $feed = ezcFeed::parse($urlAttribute->getValue());
        } catch (ezcBaseFileNotFoundException $e) {
            throw new BlockRenderException($blockValue->getType(), $e->getMessage());
        } catch (ezcFeedParseErrorException $e) {
            throw new BlockRenderException($blockValue->getType(), $e->getMessage());
        }

        $limit = (!empty($limitAttribute->getValue())) ? $limitAttribute->getValue() : 10;
        $offset = (!empty($offsetAttribute->getValue())) ? $offsetAttribute->getValue() : 0;

        $parameters['title'] = $this->getFeedTitle($feed);
        $parameters['link'] = $this->getFeedLink($feed);
        $parameters['feeds'] = $this->getFeeds($feed, $limit, $offset);

        $renderRequest->setParameters($parameters);
    }

    /**
     * Returns title from feed xml.
     *
     * @param \ezcFeed $feed
     *
     * @return string
     */
    private function getFeedTitle(ezcFeed $feed)
    {
        return (isset($feed->title)) ? $feed->title->__toString() : '';
    }

    /**
     * Returns link from feed xml.
     *
     * @param \ezcFeed $feed
     *
     * @return mixed|string
     */
    private function getFeedLink(ezcFeed $feed)
    {
        $link = $feed->link;

        return (!empty($link)) ? array_pop($link) : '';
    }

    /**
     * Returns list of feeds.
     *
     * @param \ezcFeed $feed
     * @param int     $limit
     * @param int     $offset
     *
     * @return array
     */
    private function getFeeds(ezcFeed $feed, $limit, $offset = 0)
    {
        $feeds = $this->prepareFeeds($feed);

        return \array_slice($feeds, (int)$offset, (int)$limit);
    }

    /**
     * Preparing feeds from xml objects.
     *
     * @param \ezcFeed $feed
     *
     * @return array
     */
    private function prepareFeeds(ezcFeed $feed)
    {
        $feeds = [];
        foreach ($feed->item as $item) {
            $title = isset($item->title) ? $item->title->__toString() : null;
            $description = isset($item->description) ? $item->description->__toString() : null;
            $links = [];
            if (isset($item->link)) {
                foreach ($item->link as $link) {
                    $links[] = $link->href;
                }
            }
            $feeds[] = [
                'title' => $title,
                'description' => $description,
                'links' => $links,
            ];
        }

        return $feeds;
    }
}

class_alias(RssBlockListener::class, 'EzSystems\EzPlatformPageFieldType\FieldType\Page\Block\Event\Listener\RssBlockListener');
