<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Dashboard\EventSubscriber;

use Ibexa\Contracts\Core\Repository\Events\Content\PublishVersionEvent;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\User\UserSetting\UserSetting;
use Ibexa\User\UserSetting\UserSettingService;
use PHPUnit\Framework\TestCase;

final class PublishDashboardSubscriberTest extends TestCase
{
    private const PREDEFINED_DASHBOARD_CONTAINER_REMOTE_ID = 'predefined_dashboard_container';
    private const CUSTOMIZED_DASHBOARD_REMOTE_ID = 'customized_dashboard';
    private const DASHBOARD_CONTENT_TYPE_IDENTIFIER = 'dashboard_identifier';
    private const DASHBOARD_PARAMETERS = [
        ['dashboard.content_type_identifier', null, null, self::DASHBOARD_CONTENT_TYPE_IDENTIFIER],
        ['dashboard.predefined_container_remote_id', null, null, self::PREDEFINED_DASHBOARD_CONTAINER_REMOTE_ID],
    ];

    private PublishDashboardSubscriber $publishDashboardSubscriber;

    /** @var \Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface&\PHPUnit\Framework\MockObject\MockObject */
    private ConfigResolverInterface $configResolver;

    /** @var \Ibexa\User\UserSetting\UserSettingService&\PHPUnit\Framework\MockObject\MockObject */
    private UserSettingService $userSettingService;

    public function setUp(): void
    {
        $this->configResolver = $this->createMock(ConfigResolverInterface::class);
        $this->userSettingService = $this->createMock(UserSettingService::class);

        $this->publishDashboardSubscriber = new PublishDashboardSubscriber(
            $this->configResolver,
            $this->userSettingService,
        );
    }

    public function testGetSubscribedEvents(): void
    {
        self::assertEquals(
            [
                PublishVersionEvent::class => ['onPublishDashboard'],
            ],
            PublishDashboardSubscriber::getSubscribedEvents()
        );
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\Exception
     */
    public function testOnPublishDashboard(): void
    {
        $event = $this->createEvent(
            'non_predefined_dashboard_container_remote_id',
            self::CUSTOMIZED_DASHBOARD_REMOTE_ID,
            self::DASHBOARD_CONTENT_TYPE_IDENTIFIER
        );

        $this->configureDashboardParameters();

        $userSetting = new UserSetting(['value' => 'predefined_dashboard']);
        $this->userSettingService
            ->expects(self::once())
            ->method('getUserSetting')
            ->with('active_dashboard')
            ->willReturn($userSetting);
        $this->userSettingService
            ->expects(self::once())
            ->method('setUserSetting')
            ->with('active_dashboard', self::CUSTOMIZED_DASHBOARD_REMOTE_ID);

        $this->publishDashboardSubscriber->onPublishDashboard($event);
    }

    public function testOnPublishNotDashboard(): void
    {
        $event = $this->createEvent(
            self::PREDEFINED_DASHBOARD_CONTAINER_REMOTE_ID,
            self::CUSTOMIZED_DASHBOARD_REMOTE_ID,
            'non_dashboard_content_type'
        );

        $this->configureDashboardParameters();

        $this->userSettingService
            ->expects(self::never())
            ->method('getUserSetting')
            ->with('active_dashboard');
        $this->userSettingService
            ->expects(self::never())
            ->method('setUserSetting')
            ->with('active_dashboard', self::CUSTOMIZED_DASHBOARD_REMOTE_ID);

        $this->publishDashboardSubscriber->onPublishDashboard($event);
    }

    public function testOnPublishPredefinedDashboard(): void
    {
        $event = $this->createEvent(
            self::PREDEFINED_DASHBOARD_CONTAINER_REMOTE_ID,
            self::CUSTOMIZED_DASHBOARD_REMOTE_ID,
            self::DASHBOARD_CONTENT_TYPE_IDENTIFIER
        );

        $this->configureDashboardParameters();

        $this->userSettingService
            ->expects(self::never())
            ->method('getUserSetting')
            ->with('active_dashboard');
        $this->userSettingService
            ->expects(self::never())
            ->method('setUserSetting')
            ->with('active_dashboard', self::CUSTOMIZED_DASHBOARD_REMOTE_ID);

        $this->publishDashboardSubscriber->onPublishDashboard($event);
    }

    public function testOnPublishDefaultDashboard(): void
    {
        $event = $this->createEvent(
            'non_predefined_dashboard_container_remote_id',
            'default_dashboard',
            self::DASHBOARD_CONTENT_TYPE_IDENTIFIER
        );

        $this->configureDashboardParameters();

        $userSetting = new UserSetting(['value' => 'default_dashboard']);
        $this->userSettingService
            ->expects(self::once())
            ->method('getUserSetting')
            ->with('active_dashboard')
            ->willReturn($userSetting);
        $this->userSettingService
            ->expects(self::never())
            ->method('setUserSetting')
            ->with('active_dashboard', self::CUSTOMIZED_DASHBOARD_REMOTE_ID);

        $this->publishDashboardSubscriber->onPublishDashboard($event);
    }

    private function createEvent(
        string $parentLocationRemoteId,
        string $locationRemoteId,
        string $contentTypeIdentifier
    ): PublishVersionEvent {
        $parentLocation = $this->createMock(Location::class);
        $parentLocation
            ->method('__get')
            ->with('remoteId')
            ->willReturn($parentLocationRemoteId);
        $location = $this->createMock(Location::class);
        $location
            ->method('getParentLocation')
            ->willReturn($parentLocation);
        $location
            ->method('__get')
            ->with('remoteId')
            ->willReturn($locationRemoteId);
        $contentType = $this->createMock(ContentType::class);
        $contentType
            ->method('__get')
            ->with('identifier')
            ->willReturn($contentTypeIdentifier);

        $contentInfo = $this->createMock(ContentInfo::class);
        $contentInfo
            ->method('getContentType')
            ->willReturn($contentType);
        $contentInfo
            ->method('getMainLocation')
            ->willReturn($location);

        $versionInfo = $this->createMock(VersionInfo::class);
        $versionInfo
            ->method('getContentInfo')
            ->willReturn($contentInfo);

        return new PublishVersionEvent(
            $this->createMock(Content::class),
            $versionInfo,
            []
        );
    }

    private function configureDashboardParameters(): void
    {
        $this->configResolver
            ->expects(self::atLeastOnce())
            ->method('getParameter')
            ->willReturnMap(self::DASHBOARD_PARAMETERS);
    }
}
