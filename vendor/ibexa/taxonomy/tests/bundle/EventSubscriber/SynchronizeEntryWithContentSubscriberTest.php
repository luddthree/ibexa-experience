<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Taxonomy\EventSubscriber;

use Ibexa\Contracts\Core\Repository\Events\Content\DeleteTranslationEvent;
use Ibexa\Contracts\Core\Repository\Events\Content\PublishVersionEvent;
use Ibexa\Contracts\Core\Repository\Events\Content\UpdateContentMetadataEvent;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentMetadataUpdateStruct;
use Ibexa\Contracts\Taxonomy\Event\MoveTaxonomyEntryEvent;
use Ibexa\Contracts\Taxonomy\Event\MoveTaxonomyEntryRelativeToSiblingEvent;
use Ibexa\Contracts\Taxonomy\Service\TaxonomyServiceInterface;
use Ibexa\Core\Repository\Values\Content\Content;
use Ibexa\Taxonomy\Event\Subscriber\SynchronizeEntryWithContentSubscriber;
use Ibexa\Taxonomy\Exception\TaxonomyEntryNotFoundException;
use Ibexa\Taxonomy\Repository\Content\ContentSynchronizerInterface;
use Ibexa\Taxonomy\Service\TaxonomyConfiguration;
use Ibexa\Tests\Taxonomy\ContentTestDataProvider;
use Ibexa\Tests\Taxonomy\FieldTestDataProvider;
use PHPUnit\Framework\TestCase;

final class SynchronizeEntryWithContentSubscriberTest extends TestCase
{
    /** @var \Ibexa\Contracts\Taxonomy\Service\TaxonomyServiceInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private TaxonomyServiceInterface $taxonomyService;

    /** @var \Ibexa\Taxonomy\Service\TaxonomyConfiguration|\PHPUnit\Framework\MockObject\MockObject */
    private TaxonomyConfiguration $taxonomyConfiguration;

    /**
     * @var \Ibexa\Taxonomy\Repository\Content\ContentSynchronizerInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private ContentSynchronizerInterface $contentSynchronizer;

    public function setUp(): void
    {
        $this->taxonomyService = $this->createMock(TaxonomyServiceInterface::class);
        $this->taxonomyConfiguration = $this->createMock(TaxonomyConfiguration::class);
        $this->contentSynchronizer = $this->createMock(ContentSynchronizerInterface::class);
    }

    public function testGetSubscribedEvents(): void
    {
        self::assertEquals([
            PublishVersionEvent::class => 'onVersionPublish',
            DeleteTranslationEvent::class => 'onTranslationDelete',
            UpdateContentMetadataEvent::class => 'onContentMetadataUpdate',
            MoveTaxonomyEntryEvent::class => 'onTaxonomyEntryMove',
            MoveTaxonomyEntryRelativeToSiblingEvent::class => 'onMoveTaxonomyEntryRelativeToSibling',
        ], SynchronizeEntryWithContentSubscriber::getSubscribedEvents());
    }

    public function testOnVersionPublish(): void
    {
        $this->setUpMethods();

        $subscriber = new SynchronizeEntryWithContentSubscriber(
            $this->taxonomyService,
            $this->taxonomyConfiguration,
            $this->contentSynchronizer
        );

        $event = $this->createPublishVersionEvent();
        $subscriber->onVersionPublish($event);
    }

    public function setUpMethods(): void
    {
        $this->taxonomyConfiguration
            ->method('isContentTypeAssociatedWithTaxonomy')
            ->willReturn(true);
        $this->taxonomyService
            ->expects(self::once())
            ->method('loadEntryByContentId');
        $this->contentSynchronizer
            ->expects(self::once())
            ->method('synchronize');
    }

    private function createPublishVersionEvent(): PublishVersionEvent
    {
        $content = $this->createContent();

        return new PublishVersionEvent(
            $content,
            $content->getVersionInfo(),
            [],
        );
    }

    private function createContent(): Content
    {
        return ContentTestDataProvider::getContent(
            ContentTestDataProvider::getSimpleContentType(),
            FieldTestDataProvider::getFields()
        );
    }

    public function testWhenContentTypeIsNotAssociatedWithTaxonomy(): void
    {
        $this->taxonomyConfiguration
            ->method('isContentTypeAssociatedWithTaxonomy')
            ->willReturn(false);
        $this->taxonomyService
            ->expects(self::never())
            ->method('loadEntryByContentId');
        $this->contentSynchronizer
            ->expects(self::never())
            ->method('synchronize');

        $subscriber = new SynchronizeEntryWithContentSubscriber(
            $this->taxonomyService,
            $this->taxonomyConfiguration,
            $this->contentSynchronizer
        );

        $event = $this->createPublishVersionEvent();
        $subscriber->onVersionPublish($event);
    }

    public function testOnVersionPublishWhenTaxonomyEntryNotFoundException(): void
    {
        $this->taxonomyConfiguration
            ->method('isContentTypeAssociatedWithTaxonomy')
            ->willReturn(true);
        $this->taxonomyService
            ->method('loadEntryByContentId')
            ->willThrowException(new TaxonomyEntryNotFoundException());
        $this->contentSynchronizer
            ->expects(self::never())
            ->method('synchronize');

        $subscriber = new SynchronizeEntryWithContentSubscriber(
            $this->taxonomyService,
            $this->taxonomyConfiguration,
            $this->contentSynchronizer
        );

        $event = $this->createPublishVersionEvent();
        $subscriber->onVersionPublish($event);
    }

    public function testOnTranslationDelete(): void
    {
        $this->setUpMethods();

        $subscriber = new SynchronizeEntryWithContentSubscriber(
            $this->taxonomyService,
            $this->taxonomyConfiguration,
            $this->contentSynchronizer
        );

        $event = $this->createDeleteTranslationEvent();
        $subscriber->onTranslationDelete($event);
    }

    private function createDeleteTranslationEvent(): DeleteTranslationEvent
    {
        return new DeleteTranslationEvent(ContentTestDataProvider::getContentInfo(), 'PL');
    }

    public function testOnContentMetadataUpdate(): void
    {
        $this->setUpMethods();

        $subscriber = new SynchronizeEntryWithContentSubscriber(
            $this->taxonomyService,
            $this->taxonomyConfiguration,
            $this->contentSynchronizer
        );

        $event = $this->createUpdateContentMetadataEvent();
        $subscriber->onContentMetadataUpdate($event);
    }

    private function createUpdateContentMetadataEvent(): UpdateContentMetadataEvent
    {
        $content = $this->createContent();

        return new UpdateContentMetadataEvent(
            $content,
            ContentTestDataProvider::getContentInfo(),
            new ContentMetadataUpdateStruct(),
        );
    }
}
