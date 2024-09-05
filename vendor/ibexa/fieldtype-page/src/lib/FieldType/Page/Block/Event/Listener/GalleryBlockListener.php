<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\FieldType\Page\Block\Event\Listener;

use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\SearchService;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\FieldTypePage\Event\BlockResponseEvent;
use Ibexa\FieldTypePage\Event\BlockResponseEvents;
use Ibexa\FieldTypePage\FieldType\Page\Block\Renderer\BlockRenderEvents;
use Ibexa\FieldTypePage\FieldType\Page\Block\Renderer\Event\PreRenderEvent;
use Ibexa\HttpCache\Handler\TagHandler;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class GalleryBlockListener implements EventSubscriberInterface
{
    /** @var \Ibexa\Contracts\Core\Repository\ContentService */
    private $contentService;

    /** @var \Ibexa\Contracts\Core\Repository\LocationService */
    private $locationService;

    /** @var \Ibexa\Contracts\Core\Repository\SearchService */
    private $searchService;

    /** @var \Ibexa\HttpCache\Handler\TagHandler */
    private $tagHandler;

    /**
     * @param \Ibexa\Contracts\Core\Repository\ContentService $contentService
     * @param \Ibexa\Contracts\Core\Repository\LocationService $locationService
     * @param \Ibexa\Contracts\Core\Repository\SearchService $searchService
     * @param \Ibexa\HttpCache\Handler\TagHandler $tagHandler
     */
    public function __construct(
        ContentService $contentService,
        LocationService $locationService,
        SearchService $searchService,
        TagHandler $tagHandler
    ) {
        $this->contentService = $contentService;
        $this->locationService = $locationService;
        $this->searchService = $searchService;
        $this->tagHandler = $tagHandler;
    }

    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [
            BlockRenderEvents::getBlockPreRenderEventName('gallery') => 'onBlockPreRender',
            BlockResponseEvents::getBlockResponseEventName('gallery') => 'onBlockResponse',
        ];
    }

    /**
     * @param \Ibexa\FieldTypePage\FieldType\Page\Block\Renderer\Event\PreRenderEvent $event
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function onBlockPreRender(PreRenderEvent $event)
    {
        $blockValue = $event->getBlockValue();
        $renderRequest = $event->getRenderRequest();

        $parameters = $renderRequest->getParameters();

        $contentIdAttribute = $blockValue->getAttribute('contentId');
        $contentId = (int) $contentIdAttribute->getValue();
        $location = $this->loadLocationByContentId($contentId);

        $contentArray = $this->findContentItems($location);

        $parameters['contentArray'] = $contentArray;
        $parameters['folder'] = $location->id;
        $parameters['location'] = $location;

        $renderRequest->setParameters($parameters);
    }

    /**
     * @param \Ibexa\FieldTypePage\Event\BlockResponseEvent $event
     */
    public function onBlockResponse(BlockResponseEvent $event): void
    {
        $blockValue = $event->getBlockValue();
        $contentIdAttribute = $blockValue->getAttribute('contentId');

        if (null === $contentIdAttribute) {
            return;
        }

        try {
            $contentId = (int) $contentIdAttribute->getValue();
            $location = $this->loadLocationByContentId($contentId);
            $contentItems = $this->findContentItems($location);
        } catch (\Exception $e) {
            return;
        }

        $tags = [];
        foreach ($contentItems as $content) {
            $tags[] = 'relation-' . $content->id;
        }

        $this->tagHandler->addTags($tags);
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Location $location
     *
     * @return array
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    private function findContentItems(Location $location): array
    {
        $query = new Query();
        $query->query = new Criterion\LogicalAnd(
            [
                new Criterion\Subtree($location->pathString),
                new Criterion\ContentTypeIdentifier('image'),
                new Criterion\Visibility(Criterion\Visibility::VISIBLE),
            ]
        );

        $searchHits = $this->searchService->findContent($query)->searchHits;

        $contentArray = [];
        foreach ($searchHits as $searchHit) {
            $content = $searchHit->valueObject;
            $contentArray[] = $content;
        }

        return $contentArray;
    }

    /**
     * @param int $contentId
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Location
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    private function loadLocationByContentId(int $contentId): Location
    {
        $contentInfo = $this->contentService->loadContentInfo($contentId);

        return $this->locationService->loadLocation($contentInfo->mainLocationId);
    }
}

class_alias(GalleryBlockListener::class, 'EzSystems\EzPlatformPageFieldType\FieldType\Page\Block\Event\Listener\GalleryBlockListener');
