<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Taxonomy\Event\Subscriber;

use Ibexa\Contracts\Core\Persistence\Content as PersistenceContent;
use Ibexa\Contracts\Core\Persistence\Content\Handler;
use Ibexa\Contracts\Core\Persistence\Content\Location as PersistenceLocation;
use Ibexa\Contracts\Core\Persistence\Content\Location\Handler as LocationHandler;
use Ibexa\Contracts\Core\Persistence\Handler as ContentPersistenceHandler;
use Ibexa\Contracts\Core\Repository\SearchService;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchHit;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchResult;
use Ibexa\Contracts\Core\Search\Handler as ContentSearchHandler;
use Ibexa\Contracts\Taxonomy\Event\BeforeRemoveTaxonomyEntryEvent;
use Ibexa\Contracts\Taxonomy\Event\RemoveTaxonomyEntryEvent;
use Ibexa\Contracts\Taxonomy\Search\Query\Criterion;
use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry;
use Ibexa\Core\Repository\Values\Content\Content;
use Ibexa\Core\Repository\Values\Content\VersionInfo;
use Ibexa\Taxonomy\Event\Subscriber\RemoveTaxonomyEntrySubscriber;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\Taxonomy\Event\Subscriber\RemoveTaxonomyEntrySubscriber
 */
final class RemoveTaxonomyEntrySubscriberTest extends TestCase
{
    private RemoveTaxonomyEntrySubscriber $subscriber;

    /** @var \Ibexa\Contracts\Core\Repository\SearchService&\PHPUnit\Framework\MockObject\MockObject */
    private SearchService $searchService;

    /** @var \Ibexa\Contracts\Core\Search\Handler&\PHPUnit\Framework\MockObject\MockObject */
    private ContentSearchHandler $searchHandler;

    /** @var \Ibexa\Contracts\Core\Persistence\Handler&\PHPUnit\Framework\MockObject\MockObject */
    private ContentPersistenceHandler $persistenceHandler;

    protected function setUp(): void
    {
        $this->searchService = $this->createMock(SearchService::class);
        $this->searchHandler = $this->createMock(ContentSearchHandler::class);
        $this->persistenceHandler = $this->createMock(ContentPersistenceHandler::class);
        $this->subscriber = new RemoveTaxonomyEntrySubscriber(
            $this->searchService,
            $this->searchHandler,
            $this->persistenceHandler,
        );
    }

    public function testGetSubscribedEvents(): void
    {
        $this->assertSame(
            [
                BeforeRemoveTaxonomyEntryEvent::class => 'onBeforeRemoveTaxonomyEntry',
                RemoveTaxonomyEntryEvent::class => 'onRemoveTaxonomyEntry',
            ],
            RemoveTaxonomyEntrySubscriber::getSubscribedEvents()
        );
    }

    public function testOnBeforeRemoveTaxonomyEntry(): BeforeRemoveTaxonomyEntryEvent
    {
        $entry = $this->getTaxonomyEntry();
        $content = $this->getContent();

        $query = new Query();
        $query->query = new Criterion\TaxonomyEntryId($entry->getId());

        $searchHits[] = new SearchHit([
            'valueObject' => $content,
        ]);
        $searchResult = new SearchResult(['totalCount' => 1, 'searchHits' => $searchHits]);

        $this->searchService
            ->expects(self::once())
            ->method('findContent')
            ->with($query)
            ->willReturn($searchResult);

        $beforeRemoveTaxonomyEntryEvent = new BeforeRemoveTaxonomyEntryEvent($entry);

        $this->subscriber->onBeforeRemoveTaxonomyEntry($beforeRemoveTaxonomyEntryEvent);

        self::assertSame(
            [$content->getVersionInfo()->getContentInfo()->getId()],
            $beforeRemoveTaxonomyEntryEvent->getContentIds()
        );

        return $beforeRemoveTaxonomyEntryEvent;
    }

    /**
     * @depends testOnBeforeRemoveTaxonomyEntry
     */
    public function testOnRemoveTaxonomyEntry(
        BeforeRemoveTaxonomyEntryEvent $beforeRemoveTaxonomyEntryEvent
    ): void {
        $entry = $this->getTaxonomyEntry();
        $contentIds = $beforeRemoveTaxonomyEntryEvent->getContentIds();

        $this->persistenceHandler
            ->expects(self::once())
            ->method('locationHandler')
            ->willReturn($locationHandler = $this->createMock(LocationHandler::class));

        $this->persistenceHandler
            ->expects(self::once())
            ->method('contentHandler')
            ->willReturn($contentHandler = $this->createMock(Handler::class));

        $locationHandler
            ->expects(self::once())
            ->method('loadLocationsByContent')
            ->with($contentIds[0])
            ->willReturn([$persistenceLocation = new PersistenceLocation()]);

        $contentHandler
            ->expects(self::once())
            ->method('load')
            ->with($contentIds[0])
            ->willReturn($persistenceContent = new PersistenceContent());

        $this->searchHandler
            ->expects(self::once())
            ->method('indexLocation')
            ->with($persistenceLocation);

        $this->searchHandler
            ->expects(self::once())
            ->method('indexContent')
            ->with($persistenceContent);

        $beforeRemoveTaxonomyEntryEvent = new RemoveTaxonomyEntryEvent($entry, $contentIds);

        $this->subscriber->onRemoveTaxonomyEntry($beforeRemoveTaxonomyEntryEvent);
    }

    private function getTaxonomyEntry(): TaxonomyEntry
    {
        return new TaxonomyEntry(
            1,
            'foo',
            'Foo',
            'eng-GB',
            [],
            null,
            new Content([
                'versionInfo' => new VersionInfo([
                    'contentInfo' => new ContentInfo(['id' => 5]),
                ]),
            ]),
            'foobar'
        );
    }

    private function getContent(): Content
    {
        return new Content([
            'versionInfo' => new VersionInfo([
                'contentInfo' => new ContentInfo(['id' => 6]),
            ]),
        ]);
    }
}
