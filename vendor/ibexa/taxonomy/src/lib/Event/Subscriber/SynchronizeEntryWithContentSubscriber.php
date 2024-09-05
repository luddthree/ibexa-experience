<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\Event\Subscriber;

use Ibexa\Contracts\Core\Repository\Events\Content\DeleteTranslationEvent;
use Ibexa\Contracts\Core\Repository\Events\Content\PublishVersionEvent;
use Ibexa\Contracts\Core\Repository\Events\Content\UpdateContentMetadataEvent;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Contracts\Taxonomy\Event\MoveTaxonomyEntryEvent;
use Ibexa\Contracts\Taxonomy\Event\MoveTaxonomyEntryRelativeToSiblingEvent;
use Ibexa\Contracts\Taxonomy\Service\TaxonomyServiceInterface;
use Ibexa\Taxonomy\Exception\TaxonomyEntryNotFoundException;
use Ibexa\Taxonomy\Repository\Content\ContentSynchronizerInterface;
use Ibexa\Taxonomy\Service\TaxonomyConfiguration;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @internal
 */
final class SynchronizeEntryWithContentSubscriber implements EventSubscriberInterface
{
    private TaxonomyServiceInterface $taxonomyService;

    private TaxonomyConfiguration $taxonomyConfiguration;

    private ContentSynchronizerInterface $contentSynchronizer;

    public function __construct(
        TaxonomyServiceInterface $taxonomyService,
        TaxonomyConfiguration $taxonomyConfiguration,
        ContentSynchronizerInterface $contentSynchronizer
    ) {
        $this->taxonomyService = $taxonomyService;
        $this->taxonomyConfiguration = $taxonomyConfiguration;
        $this->contentSynchronizer = $contentSynchronizer;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            PublishVersionEvent::class => 'onVersionPublish',
            DeleteTranslationEvent::class => 'onTranslationDelete',
            UpdateContentMetadataEvent::class => 'onContentMetadataUpdate',
            MoveTaxonomyEntryEvent::class => 'onTaxonomyEntryMove',
            MoveTaxonomyEntryRelativeToSiblingEvent::class => 'onMoveTaxonomyEntryRelativeToSibling',
        ];
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function onVersionPublish(PublishVersionEvent $event): void
    {
        $content = $event->getContent();

        $this->doSynchronizeContent($content->getContentType(), $content->id);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function onTranslationDelete(DeleteTranslationEvent $event): void
    {
        $contentInfo = $event->getContentInfo();

        $this->doSynchronizeContent($contentInfo->getContentType(), $contentInfo->id);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function onContentMetadataUpdate(UpdateContentMetadataEvent $event): void
    {
        $content = $event->getContent();

        $this->doSynchronizeContent($content->getContentType(), $content->id);
    }

    public function onTaxonomyEntryMove(MoveTaxonomyEntryEvent $event): void
    {
        $content = $event->getTaxonomyEntry()->getContent();

        $this->doReverseSynchronizeContent($content->getContentType(), $content->id);
    }

    public function onMoveTaxonomyEntryRelativeToSibling(MoveTaxonomyEntryRelativeToSiblingEvent $event): void
    {
        $content = $event->getTaxonomyEntry()->getContent();

        $this->doReverseSynchronizeContent($content->getContentType(), $content->id);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    private function doSynchronizeContent(ContentType $contentType, int $contentId): void
    {
        if (!$this->taxonomyConfiguration->isContentTypeAssociatedWithTaxonomy($contentType)) {
            return;
        }

        try {
            $taxonomyEntry = $this->taxonomyService->loadEntryByContentId($contentId);
        } catch (TaxonomyEntryNotFoundException $e) {
            return;
        }

        $this->contentSynchronizer->synchronize($taxonomyEntry);
    }

    private function doReverseSynchronizeContent(ContentType $contentType, int $contentId): void
    {
        if (!$this->taxonomyConfiguration->isContentTypeAssociatedWithTaxonomy($contentType)) {
            return;
        }

        try {
            $taxonomyEntry = $this->taxonomyService->loadEntryByContentId($contentId);
        } catch (TaxonomyEntryNotFoundException $e) {
            return;
        }

        $this->contentSynchronizer->reverseSynchronize($taxonomyEntry);
    }
}
