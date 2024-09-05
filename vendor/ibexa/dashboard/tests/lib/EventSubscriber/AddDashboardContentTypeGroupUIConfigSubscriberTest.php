<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Dashboard\EventSubscriber;

use Ibexa\AdminUi\Event\AddContentTypeGroupToUIConfigEvent;
use Ibexa\Bundle\Dashboard\DependencyInjection\Parser\Dashboard;
use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Dashboard\EventSubscriber\AddDashboardContentTypeGroupUIConfigSubscriber;
use Ibexa\Tests\Dashboard\PhpUnit\ContentItemOfContentTypeMockTrait;
use PHPUnit\Framework\TestCase;

final class AddDashboardContentTypeGroupUIConfigSubscriberTest extends TestCase
{
    use ContentItemOfContentTypeMockTrait;

    private AddDashboardContentTypeGroupUIConfigSubscriber $addDashboardContentTypeGroupUIConfigSubscriber;

    public function setUp(): void
    {
        $configResolver = $this->createMock(ConfigResolverInterface::class);
        $configResolver
            ->method('getParameter')
            ->with(AddDashboardContentTypeGroupUIConfigSubscriber::CONTENT_TYPE_IDENTIFIER_PARAM_NAME)
            ->willReturn(Dashboard::DASHBOARD_CONTENT_TYPE_GROUP_IDENTIFIER)
        ;
        $contentTypeService = $this->createMock(ContentTypeService::class);
        $contentTypeService
            ->method('loadContentTypeGroupByIdentifier')
            ->with(Dashboard::DASHBOARD_CONTENT_TYPE_GROUP_IDENTIFIER)
            ->willReturn($this->mockContentTypeGroup('Dashboard'));

        $this->addDashboardContentTypeGroupUIConfigSubscriber = new AddDashboardContentTypeGroupUIConfigSubscriber(
            $configResolver,
            $contentTypeService
        );
    }

    public function testGetSubscribedEvents(): void
    {
        self::assertEquals(
            [
                AddContentTypeGroupToUIConfigEvent::class => ['addDashboardContentTypeGroup'],
            ],
            AddDashboardContentTypeGroupUIConfigSubscriber::getSubscribedEvents()
        );
    }

    public function testAddDashboardContentTypeGroup(): void
    {
        $event = $this->createEvent();
        $this->addDashboardContentTypeGroupUIConfigSubscriber->addDashboardContentTypeGroup($event);
        $groups = $event->getContentTypeGroups();
        $groupIdentifiers = array_flip(
            array_map(
                static fn ($group): string => $group->identifier,
                $groups,
            ),
        );

        self::assertCount(2, $groups);
        self::assertArrayHasKey('Dashboard', $groupIdentifiers);
    }

    private function createEvent(): AddContentTypeGroupToUIConfigEvent
    {
        $contentTypeGroups = [
            $this->mockContentTypeGroup('content'),
        ];

        return new AddContentTypeGroupToUIConfigEvent(
            $contentTypeGroups
        );
    }
}
