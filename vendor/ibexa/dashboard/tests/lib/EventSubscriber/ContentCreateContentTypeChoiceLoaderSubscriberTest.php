<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Dashboard\EventSubscriber;

use Ibexa\AdminUi\Form\Type\Event\ContentCreateContentTypeChoiceLoaderEvent;
use Ibexa\Contracts\Core\Persistence\Content\Location as PersistenceLocation;
use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Dashboard\EventSubscriber\ContentCreateContentTypeChoiceLoaderSubscriber;
use Ibexa\Tests\Dashboard\PhpUnit\ContentItemOfContentTypeMockTrait;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\Dashboard\EventSubscriber\ContentCreateContentTypeChoiceLoaderSubscriber
 */
final class ContentCreateContentTypeChoiceLoaderSubscriberTest extends TestCase
{
    use ContentItemOfContentTypeMockTrait;

    private const DASHBOARD_CONTENT_TYPE_IDENTIFIER = 'dashboard_identifier';

    private ContentCreateContentTypeChoiceLoaderSubscriber $subscriber;

    /** @var \Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface&\PHPUnit\Framework\MockObject\MockObject */
    private ConfigResolverInterface $configResolver;

    /** @var \Ibexa\Contracts\Core\Persistence\Content\Location\Handler&\PHPUnit\Framework\MockObject\MockObject */
    private PersistenceLocation\Handler $locationHandler;

    public function setUp(): void
    {
        $this->configResolver = $this->createMock(ConfigResolverInterface::class);
        $contentTypeGroups = [
            $this->mockContentTypeGroup('dashboard_group1'),
            $this->mockContentTypeGroup('dashboard_group2'),
        ];
        $contentType = $this->mockDashboardContentType($contentTypeGroups);
        $contentTypeService = $this->createMock(ContentTypeService::class);
        $contentTypeService
            ->method('loadContentTypeByIdentifier')
            ->willReturn($contentType)
        ;
        $this->locationHandler = $this->createMock(PersistenceLocation\Handler::class);
        $this->subscriber = new ContentCreateContentTypeChoiceLoaderSubscriber(
            $this->configResolver,
            $contentTypeService,
            $this->locationHandler
        );
    }

    public function testGetSubscribedEvents(): void
    {
        self::assertEquals(
            [
                'admin_ui.content_create.content_type_resolve' => 'removeDashboardContentType',
            ],
            ContentCreateContentTypeChoiceLoaderSubscriber::getSubscribedEvents()
        );
    }

    public function testRemoveDashboardContentType(): void
    {
        $dashboardContainerLocation = $this->getPersistenceLocation('/1/56/');
        $this->locationHandler
            ->method('loadByRemoteId')
            ->willReturn($dashboardContainerLocation);

        $dashboardContentType = $this->mockContentType(self::DASHBOARD_CONTENT_TYPE_IDENTIFIER);
        $event = new ContentCreateContentTypeChoiceLoaderEvent(
            [
                'content_group' => [$this->mockContentType('folder'), $this->mockContentType('article')],
                'dashboard_group1' => [$dashboardContentType],
                'dashboard_group2' => [$dashboardContentType],
            ],
            $this->getLocation('/1/2/')
        );

        $this->configureDashboardParameters();

        $this->subscriber->removeDashboardContentType($event);

        $contentTypeGroups = $event->getContentTypeGroups();

        self::assertArrayHasKey('content_group', $contentTypeGroups);
        self::assertArrayNotHasKey('dashboard_group1', $contentTypeGroups);
        self::assertArrayNotHasKey('dashboard_group2', $contentTypeGroups);
    }

    private function configureDashboardParameters(): void
    {
        $this->configResolver
            ->method('getParameter')
            ->willReturn(self::DASHBOARD_CONTENT_TYPE_IDENTIFIER)
        ;
    }

    private function getLocation(string $pathString): Location
    {
        $location = $this->createMock(Location::class);
        $location
            ->method('getPathString')
            ->willReturn($pathString);

        return $location;
    }

    private function getPersistenceLocation(string $pathString): PersistenceLocation
    {
        return new PersistenceLocation(['pathString' => $pathString]);
    }
}
