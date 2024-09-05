<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\FieldType\Page\Block\Event\Listener;

use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\SearchService;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\FieldTypePage\FieldType\Page\Block\Renderer\BlockRenderEvents;
use Ibexa\FieldTypePage\FieldType\Page\Block\Renderer\Event\PreRenderEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class KeywordBlockListener implements EventSubscriberInterface
{
    /** @var \Ibexa\Contracts\Core\Repository\LocationService */
    private $locationService;

    /** @var \Ibexa\Contracts\Core\Repository\SearchService */
    private $searchService;

    /**
     * KeywordBlockListener constructor.
     *
     * @param \Ibexa\Contracts\Core\Repository\LocationService $locationService
     * @param \Ibexa\Contracts\Core\Repository\SearchService $searchService
     */
    public function __construct(LocationService $locationService, SearchService $searchService)
    {
        $this->locationService = $locationService;
        $this->searchService = $searchService;
    }

    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [
            BlockRenderEvents::getBlockPreRenderEventName('keyword') => 'onBlockPreRender',
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

        $attributes = $blockValue->getAttributes();

        $keywords = array_map('trim', explode(',', $attributes['keywords']));
        $criteria = [];
        foreach ($keywords as $keyword) {
            $criteria[] = new Criterion\FullText($keyword);
        }

        $query = new Query();
        $query->limit = (int)$attributes['limit'];
        $query->query = new Criterion\LogicalAnd([
            new Criterion\ContentTypeIdentifier(strstr($attributes['contentType'], ',') ? explode(',', $attributes['contentType']) : $attributes['contentType']),
            new Criterion\Visibility(Criterion\Visibility::VISIBLE),
            new Criterion\LogicalOr($criteria),
        ]);

        $searchHits = $this->searchService->findContent($query)->searchHits;

        $contentArray = [];
        foreach ($searchHits as $key => $searchHit) {
            $content = $searchHit->valueObject;
            $contentArray[$key]['content'] = $content;
            $contentArray[$key]['location'] = $this->locationService->loadLocation($content->contentInfo->mainLocationId);
        }

        $parameters['keywords'] = $attributes['keywords'];
        $parameters['contentArray'] = $contentArray;

        $renderRequest->setParameters($parameters);
    }
}

class_alias(KeywordBlockListener::class, 'EzSystems\EzPlatformPageFieldType\FieldType\Page\Block\Event\Listener\KeywordBlockListener');
