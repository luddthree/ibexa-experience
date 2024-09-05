<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\Event\Subscriber;

use Ibexa\Contracts\Core\Persistence\Handler as ContentPersistenceHandler;
use Ibexa\Contracts\Core\Repository\SearchService;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Search\Handler as ContentSearchHandler;
use Ibexa\Contracts\Taxonomy\Event\BeforeRemoveTaxonomyEntryEvent;
use Ibexa\Contracts\Taxonomy\Event\RemoveTaxonomyEntryEvent;
use Ibexa\Contracts\Taxonomy\Search\Query\Criterion;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class RemoveTaxonomyEntrySubscriber implements EventSubscriberInterface
{
    private SearchService $searchService;

    private ContentSearchHandler $searchHandler;

    private ContentPersistenceHandler $persistenceHandler;

    public function __construct(
        SearchService $searchService,
        ContentSearchHandler $searchHandler,
        ContentPersistenceHandler $persistenceHandler
    ) {
        $this->searchService = $searchService;
        $this->searchHandler = $searchHandler;
        $this->persistenceHandler = $persistenceHandler;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            BeforeRemoveTaxonomyEntryEvent::class => 'onBeforeRemoveTaxonomyEntry',
            RemoveTaxonomyEntryEvent::class => 'onRemoveTaxonomyEntry',
        ];
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function onBeforeRemoveTaxonomyEntry(BeforeRemoveTaxonomyEntryEvent $event): void
    {
        $query = new Query();
        $query->query = new Criterion\TaxonomyEntryId($event->getTaxonomyEntry()->getId());
        $searchHits = $this->searchService->findContent($query)->getIterator();

        $contentIds = [];
        foreach ($searchHits as $searchHit) {
            $contentId = $searchHit->valueObject->getVersionInfo()->getContentInfo()->getId();
            $contentIds[] = $contentId;
        }

        $event->setContentIds($contentIds);
    }

    public function onRemoveTaxonomyEntry(RemoveTaxonomyEntryEvent $event): void
    {
        $persistenceHandler = $this->persistenceHandler;

        foreach ($event->getContentIds() as $contentId) {
            $locations = $persistenceHandler->locationHandler()->loadLocationsByContent($contentId);

            foreach ($locations as $location) {
                $this->searchHandler->indexLocation($location);
            }

            $this->searchHandler->indexContent(
                $persistenceHandler->contentHandler()->load($contentId)
            );
        }
    }
}
