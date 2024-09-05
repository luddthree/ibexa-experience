<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\FieldType\Page\Block\Event\Listener;

use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\SearchService;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\Content\LocationQuery;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\FieldTypePage\Event\BlockResponseEvent;
use Ibexa\FieldTypePage\Event\BlockResponseEvents;
use Ibexa\FieldTypePage\FieldType\Page\Block\Renderer\BlockRenderEvents;
use Ibexa\FieldTypePage\FieldType\Page\Block\Renderer\Event\PreRenderEvent;
use Ibexa\HttpCache\Handler\TagHandler;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ContentListBlockListener implements EventSubscriberInterface
{
    /** @var \Ibexa\Contracts\Core\Repository\ContentService */
    private $contentService;

    /** @var \Ibexa\Contracts\Core\Repository\SearchService */
    private $searchService;

    /** @var \Ibexa\HttpCache\Handler\TagHandler */
    private $tagHandler;

    /** @var \Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface */
    private $configResolver;

    public function __construct(
        ContentService $contentService,
        SearchService $searchService,
        TagHandler $tagHandler,
        ConfigResolverInterface $configResolver
    ) {
        $this->contentService = $contentService;
        $this->searchService = $searchService;
        $this->tagHandler = $tagHandler;
        $this->configResolver = $configResolver;
    }

    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [
            BlockRenderEvents::getBlockPreRenderEventName('contentlist') => 'onBlockPreRender',
            BlockResponseEvents::getBlockResponseEventName('contentlist') => 'onBlockResponse',
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
        $languages = $this->configResolver->getParameter('languages');

        $contentIdAttribute = $blockValue->getAttribute('contentId');
        $contentTypeAttribute = $blockValue->getAttribute('contentType');
        $limitAttribute = $blockValue->getAttribute('limit');

        $parentContentInfo = $this->contentService->loadContentInfo($contentIdAttribute->getValue());

        $limitValue = (int)$limitAttribute->getValue();
        $contentTypeValue = $contentTypeAttribute->getValue();

        $searchHits = $parentContentInfo->isTrashed()
            ? []
            : $this->findContentItems($limitValue, $parentContentInfo, $contentTypeValue);

        $contentArray = [];
        foreach ($searchHits as $key => $searchHit) {
            $location = $searchHit->valueObject;
            $contentInfo = $location->getContentInfo();
            $content = $this->contentService->loadContentByContentInfo(
                $contentInfo,
                $languages,
                $contentInfo->currentVersionNo
            );
            $contentArray[$key]['content'] = $content;
            $contentArray[$key]['location'] = $location;
        }

        $parameters['parentName'] = $parentContentInfo->name;
        $parameters['contentArray'] = $contentArray;

        $renderRequest->setParameters($parameters);
    }

    /**
     * @param int $limitValue
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo $parentContentInfo
     * @param string $contentTypeValue
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchHit[]
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    private function findContentItems(
        int $limitValue,
        ContentInfo $parentContentInfo,
        string $contentTypeValue
    ): array {
        $query = new LocationQuery();
        $query->limit = $limitValue;

        $query->query = new Criterion\LogicalAnd(
            [
                new Criterion\ParentLocationId($parentContentInfo->mainLocationId),
                new Criterion\ContentTypeIdentifier(explode(',', $contentTypeValue)),
                new Criterion\Visibility(Criterion\Visibility::VISIBLE),
            ]
        );

        return $this->searchService->findLocations($query)->searchHits;
    }

    /**
     * @param \Ibexa\FieldTypePage\Event\BlockResponseEvent $event
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function onBlockResponse(BlockResponseEvent $event): void
    {
        $blockValue = $event->getBlockValue();

        $contentIdAttribute = $blockValue->getAttribute('contentId');
        $contentTypeAttribute = $blockValue->getAttribute('contentType');
        $limitAttribute = $blockValue->getAttribute('limit');

        if (null === $contentIdAttribute || null === $contentTypeAttribute || null === $limitAttribute) {
            return;
        }

        $limitValue = (int)$limitAttribute->getValue();
        $contentTypeValue = $contentTypeAttribute->getValue();

        try {
            $parentContentInfo = $this->contentService->loadContentInfo((int)$contentIdAttribute->getValue());
            $searchHits = $this->findContentItems($limitValue, $parentContentInfo, $contentTypeValue);
        } catch (\Exception $e) {
            return;
        }

        $tags = [];
        foreach ($searchHits as $searchHit) {
            $location = $searchHit->valueObject;
            $tags[] = 'relation-' . $location->contentId;
        }

        $this->tagHandler->addTags($tags);
    }
}

class_alias(ContentListBlockListener::class, 'EzSystems\EzPlatformPageFieldType\FieldType\Page\Block\Event\Listener\ContentListBlockListener');
