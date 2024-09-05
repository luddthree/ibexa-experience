<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\Event\Subscriber;

use DateTimeInterface;
use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue;
use Ibexa\Core\MVC\Symfony\View\CachableView;
use Ibexa\Core\MVC\Symfony\View\ContentValueView;
use Ibexa\Core\MVC\Symfony\View\LocationValueView;
use Ibexa\FieldTypePage\Event\BlockResponseEvent;
use Ibexa\FieldTypePage\Event\BlockResponseEvents;
use Ibexa\FieldTypePage\FieldType\Page\Block\Context\ContentViewBlockContext;
use Ibexa\FieldTypePage\FieldType\Page\Block\Relation\RelationCollector;
use Ibexa\FieldTypePage\FieldType\Page\FieldDefinitionLocator;
use Ibexa\HttpCache\Handler\TagHandler;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class TagBlockResponseSubscriber implements EventSubscriberInterface
{
    /** @var \Ibexa\HttpCache\Handler\TagHandler */
    private $tagHandler;

    /** @var \Ibexa\FieldTypePage\FieldType\Page\FieldDefinitionLocator */
    private $pageFieldDefinitionLocator;

    /** @var \Ibexa\Contracts\Core\Repository\ContentTypeService */
    private $contentTypeService;

    /** @var \Ibexa\Contracts\Core\Repository\ContentService */
    private $contentService;

    /** @var \Ibexa\FieldTypePage\FieldType\Page\Block\Relation\RelationCollector */
    private $relationCollector;

    /** @var \Ibexa\Contracts\Core\Repository\Repository */
    private $repository;

    /** @var \Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface */
    private $configResolver;

    /** @var string */
    private $userHashHeaderName;

    public function __construct(
        TagHandler $tagHandler,
        FieldDefinitionLocator $pageFieldDefinitionLocator,
        ContentTypeService $contentTypeService,
        ContentService $contentService,
        RelationCollector $relationCollector,
        Repository $repository,
        ConfigResolverInterface $configResolver,
        string $userHashHeaderName
    ) {
        $this->tagHandler = $tagHandler;
        $this->pageFieldDefinitionLocator = $pageFieldDefinitionLocator;
        $this->contentTypeService = $contentTypeService;
        $this->contentService = $contentService;
        $this->relationCollector = $relationCollector;
        $this->repository = $repository;
        $this->configResolver = $configResolver;
        $this->userHashHeaderName = $userHashHeaderName;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            BlockResponseEvents::BLOCK_RESPONSE => ['onBlockResponse', 0],
            KernelEvents::RESPONSE => ['onResponse', 5],
        ];
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    public function onBlockResponse(BlockResponseEvent $event): void
    {
        $blockContext = $event->getBlockContext();
        $blockValue = $event->getBlockValue();

        if (!$blockContext instanceof ContentViewBlockContext) {
            return;
        }

        $content = $blockContext->getContent();
        $location = $blockContext->getLocation();

        $this->addTags($content, $location, $blockValue);

        $response = $event->getResponse();

        if (!$this->configResolver->getParameter('content.ttl_cache')) {
            $response->setPrivate();

            return;
        }

        $ttl = $this->getBlockVisibilityTtl($event->getBlockValue(), $response->getDate());

        if ($response->isCacheable() && $response->getTtl() <= $ttl) {
            return;
        }

        $response->setPublic();
        $response->setSharedMaxAge($ttl);
        $response->setVary($this->userHashHeaderName, false);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function onResponse(ResponseEvent $event): void
    {
        $view = $event->getRequest()->attributes->get('view');
        if (!$view instanceof CachableView || !$view->isCacheEnabled()) {
            return;
        }

        if (!$view instanceof ContentValueView || !$view instanceof LocationValueView) {
            return;
        }

        $viewContent = $view->getContent();
        // @todo workaround due to ParameterProvider setting different blocks
        // using sudo as it is not required to double check for Content permissions on this event level
        $content = $this->repository->sudo(
            function () use ($viewContent) {
                return $this->contentService->loadContent(
                    $viewContent->id,
                    $viewContent->versionInfo->languageCodes,
                    $viewContent->versionInfo->versionNo
                );
            }
        );

        $location = $view->getLocation();
        $contentType = $this->contentTypeService->loadContentType($content->contentInfo->contentTypeId);
        $fieldDefinition = $this->pageFieldDefinitionLocator->locate($content, $contentType);

        // Content doesn't have Page FieldType
        if (null === $fieldDefinition) {
            return;
        }

        $response = $event->getResponse();
        $response->setVary([$this->userHashHeaderName, 'X-Editorial-Mode'], false);

        /** @var \Ibexa\FieldTypePage\FieldType\LandingPage\Value $fieldValue */
        $fieldValue = $content->getFieldValue($fieldDefinition->identifier);
        $page = $fieldValue->getPage();

        $blockTtlValues = [];
        foreach ($page->getZones() as $zone) {
            foreach ($zone->getBlocks() as $blockValue) {
                $blockTtlValues[] = $this->getBlockVisibilityTtl($blockValue, $response->getDate());
                $this->addTags($content, $location, $blockValue);
            }
        }

        if (empty($blockTtlValues)) {
            return;
        }

        $ttl = min($blockTtlValues);

        if ($response->isCacheable() && $response->getTtl() <= $ttl) {
            return;
        }

        $response->setPublic();
        $response->setSharedMaxAge($ttl);
        $response->setMaxAge($ttl);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    private function addTags(Content $content, ?Location $location, BlockValue $blockValue): void
    {
        $this->tagHandler->addContentTags([$content->id]);
        if (null !== $location) {
            $this->tagHandler->addLocationTags([$location->id]);
        }

        $contentType = $this->contentTypeService->loadContentType($content->contentInfo->contentTypeId);
        $fieldDefinition = $this->pageFieldDefinitionLocator->locate($content, $contentType);
        /** @var \Ibexa\FieldTypePage\FieldType\LandingPage\Value $fieldValue */
        $fieldValue = $content->getFieldValue($fieldDefinition->identifier);

        $relatedContentIds = $this->relationCollector->collect($fieldValue, $blockValue);
        $this->tagHandler->addRelationTags($relatedContentIds);
    }

    private function getBlockVisibilityTtl(BlockValue $blockValue, DateTimeInterface $referenceDate): int
    {
        $sinceDate = $blockValue->getSince();
        $tillDate = $blockValue->getTill();
        $ttl = (int) $this->configResolver->getParameter('content.default_ttl');
        $timestamps = [];

        if (null !== $sinceDate && $sinceDate > $referenceDate) {
            $timestamps[] = $sinceDate->getTimestamp();
        }

        if (null !== $tillDate && $tillDate > $referenceDate) {
            $timestamps[] = $tillDate->getTimestamp();
        }

        if (!empty($timestamps)) {
            $nearest = min($timestamps);
            $ttl = $nearest - $referenceDate->getTimestamp();
        }

        return $ttl;
    }
}

class_alias(TagBlockResponseSubscriber::class, 'EzSystems\EzPlatformPageFieldType\Event\Subscriber\TagBlockResponseSubscriber');
