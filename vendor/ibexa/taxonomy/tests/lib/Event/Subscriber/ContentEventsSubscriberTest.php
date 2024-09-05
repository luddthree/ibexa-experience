<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Taxonomy\Event\Subscriber;

use Doctrine\ORM\EntityManagerInterface;
use Ibexa\AdminUi\Event\Options;
use Ibexa\Contracts\AdminUi\Event\ContentProxyCreateEvent;
use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\Events\Content\DeleteContentEvent;
use Ibexa\Contracts\Core\Repository\Events\Content\PublishVersionEvent;
use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Taxonomy\Service\TaxonomyServiceInterface;
use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntryCreateStruct;
use Ibexa\Core\Repository\Values\Content\Location;
use Ibexa\Core\Repository\Values\Content\VersionInfo;
use Ibexa\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Taxonomy\Event\Subscriber\ContentEventsSubscriber;
use Ibexa\Taxonomy\Exception\TaxonomyEntryInvalidParentException;
use Ibexa\Taxonomy\Service\TaxonomyConfiguration;
use Ibexa\Tests\Taxonomy\ContentTestDataProvider;
use Ibexa\Tests\Taxonomy\FieldTestDataProvider;
use Ibexa\Tests\Taxonomy\TaxonomyEntryTestDataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\Taxonomy\Event\Subscriber\ContentEventsSubscriber
 */
final class ContentEventsSubscriberTest extends TestCase
{
    public const IBEXA_ENTRY_ASSIGNMENT_TYPE_IDENTIFIER = 'ibexa_entry_assignment';

    /** @var \Doctrine\ORM\EntityManagerInterface|\PHPUnit\Framework\MockObject\MockObject */
    private EntityManagerInterface $entityManager;

    /** @var \Ibexa\Contracts\Taxonomy\Service\TaxonomyServiceInterface|\PHPUnit\Framework\MockObject\MockObject */
    private TaxonomyServiceInterface $taxonomyService;

    /** @var \Ibexa\Taxonomy\Service\TaxonomyConfiguration|\PHPUnit\Framework\MockObject\MockObject */
    private TaxonomyConfiguration $taxonomyConfiguration;

    /** @var \Ibexa\Contracts\Core\Repository\LocationService|\PHPUnit\Framework\MockObject\MockObject */
    private LocationService $locationService;

    /** @var \Ibexa\Contracts\Core\Repository\ContentService|\PHPUnit\Framework\MockObject\MockObject */
    private ContentService $contentService;

    private ContentEventsSubscriber $subscriber;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->taxonomyService = $this->createMock(TaxonomyServiceInterface::class);
        $this->taxonomyConfiguration = $this->createMock(TaxonomyConfiguration::class);
        $this->locationService = $this->createMock(LocationService::class);
        $this->contentService = $this->createMock(ContentService::class);
        $this->subscriber = new ContentEventsSubscriber(
            $this->entityManager,
            $this->taxonomyService,
            $this->taxonomyConfiguration,
            $this->locationService,
            $this->contentService,
        );
    }

    public function testGetSubscribedEvents(): void
    {
        $this->assertSame(
            [
                DeleteContentEvent::class => 'onContentDelete',
                PublishVersionEvent::class => 'onVersionPublish',
                ContentProxyCreateEvent::class => 'onContentProxyCreate',
            ],
            ContentEventsSubscriber::getSubscribedEvents()
        );
    }

    public function testOnContentDelete(): void
    {
        $contentType = ContentTestDataProvider::getSimpleContentType();
        $contentInfo = ContentTestDataProvider::getContentInfo();

        $event = new DeleteContentEvent(
            [],
            $contentInfo
        );

        $this->taxonomyConfiguration
            ->method('getTaxonomyForContentType')
            ->with($contentType)
            ->willReturn('example_taxonomy');

        $this->taxonomyConfiguration
            ->method('isContentTypeAssociatedWithTaxonomy')
            ->with($contentType)
            ->willReturn(true);

        $taxonomyEntry = TaxonomyEntryTestDataProvider::getTaxonomyEntry(
            null,
            ContentTestDataProvider::getContent($contentType, []),
        );

        $this->taxonomyService
            ->method('loadEntryByContentId')
            ->with(1)
            ->willReturn($taxonomyEntry);

        $this->taxonomyService
            ->expects(self::once())
            ->method('removeEntry')
            ->with($taxonomyEntry);

        $this->subscriber->onContentDelete($event);
    }

    public function testOnVersionPublish(): void
    {
        $contentType = ContentTestDataProvider::getSimpleContentType();
        $parent = TaxonomyEntryTestDataProvider::getRootTaxonomyEntry();
        $content = ContentTestDataProvider::getContent($contentType, FieldTestDataProvider::getFields($parent));
        $versionInfo = new VersionInfo([
            'versionNo' => 1,
        ]);

        $event = new PublishVersionEvent(
            $content,
            $versionInfo,
            ['eng-GB'],
        );

        $this->taxonomyConfiguration
            ->method('getTaxonomyForContentType')
            ->with($contentType)
            ->willReturn('example_taxonomy');

        $this->taxonomyConfiguration
            ->method('isContentTypeAssociatedWithTaxonomy')
            ->with($contentType)
            ->willReturn(true);

        $this->taxonomyConfiguration
            ->method('getEntryIdentifierFieldFromContent')
            ->with($content)
            ->willReturn('example_entry');

        $this->taxonomyConfiguration
            ->method('getEntryParentFieldFromContent')
            ->with($content)
            ->willReturn($parent);

        $taxonomyEntryCreateStruct = new TaxonomyEntryCreateStruct(
            'example_entry',
            'example_taxonomy',
            $parent,
            $content,
        );

        $this->taxonomyService
            ->expects(self::once())
            ->method('createEntry')
            ->with($taxonomyEntryCreateStruct);

        $this->subscriber->onVersionPublish($event);
    }

    /**
     * @throws \Ibexa\Taxonomy\Exception\TaxonomyEntryInvalidParentException
     */
    public function testOnContentProxyCreate(): void
    {
        $contentType = ContentTestDataProvider::getSimpleContentType();
        $parent = TaxonomyEntryTestDataProvider::getRootTaxonomyEntry();
        $content = ContentTestDataProvider::getContent($contentType, FieldTestDataProvider::getFields($parent));

        $event = new ContentProxyCreateEvent(
            $contentType,
            'eng-GB',
            1,
            new Options([
                ContentProxyCreateEvent::OPTION_CONTENT_DRAFT => $content,
            ]),
        );

        $this->mockOnContentProxyCreateMethods($contentType);

        $this->taxonomyConfiguration
            ->expects($this->once())
            ->method('getFieldMappings')
            ->willReturn(['parent' => 'example_taxonomy']);

        $this->subscriber->onContentProxyCreate($event);
    }

    /**
     * @throws \Ibexa\Taxonomy\Exception\TaxonomyEntryInvalidParentException
     */
    public function testOnContentProxyCreateThrowExceptionIfOutsideParent(): void
    {
        $contentType = ContentTestDataProvider::getSimpleContentType();
        $parent = TaxonomyEntryTestDataProvider::getRootTaxonomyEntry();
        $content = ContentTestDataProvider::getContent($contentType, FieldTestDataProvider::getFields($parent));

        $event = new ContentProxyCreateEvent(
            $contentType,
            'eng-GB',
            10,
            new Options([
                ContentProxyCreateEvent::OPTION_CONTENT_DRAFT => $content,
            ]),
        );

        $this->mockOnContentProxyCreateMethods($contentType);

        self::expectException(TaxonomyEntryInvalidParentException::class);

        $this->subscriber->onContentProxyCreate($event);
    }

    private function mockOnContentProxyCreateMethods(ContentType $contentType): void
    {
        $location = $this->createMock(Location::class);
        $location->method('__get')->with('id')->willReturn(1);

        $this->taxonomyConfiguration
            ->method('getTaxonomyForContentType')
            ->with($contentType)
            ->willReturn('example_taxonomy');

        $this->taxonomyConfiguration
            ->method('isContentTypeAssociatedWithTaxonomy')
            ->with($contentType)
            ->willReturn(true);

        $this->taxonomyConfiguration
            ->method('getConfigForTaxonomy')
            ->with('example_taxonomy', TaxonomyConfiguration::CONFIG_PARENT_LOCATION_REMOTE_ID)
            ->willReturn('taxonomy_folder');

        $this->locationService
            ->method('loadLocationByRemoteId')
            ->with('taxonomy_folder')
            ->willReturn($location);
    }
}
