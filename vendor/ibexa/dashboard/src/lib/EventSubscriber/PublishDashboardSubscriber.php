<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Dashboard\EventSubscriber;

use Ibexa\Contracts\Core\Repository\Events\Content\PublishVersionEvent;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Dashboard\Specification\IsDashboardContentType;
use Ibexa\Dashboard\Specification\IsPredefinedDashboard;
use Ibexa\User\UserSetting\UserSettingService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class PublishDashboardSubscriber implements EventSubscriberInterface
{
    private ConfigResolverInterface $configResolver;

    private UserSettingService $userSettingService;

    public function __construct(
        ConfigResolverInterface $configResolver,
        UserSettingService $userSettingService
    ) {
        $this->configResolver = $configResolver;
        $this->userSettingService = $userSettingService;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            PublishVersionEvent::class => ['onPublishDashboard'],
        ];
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function onPublishDashboard(PublishVersionEvent $event): void
    {
        $contentInfo = $event->getVersionInfo()->getContentInfo();
        $contentType = $contentInfo->getContentType();
        $location = $contentInfo->getMainLocation();

        if ($location === null || !(new IsDashboardContentType($this->configResolver))->isSatisfiedBy($contentType)) {
            return;
        }

        if ($this->isPredefinedDashboard($location)) {
            return;
        }

        $activeDashboardRemoteId = $this->userSettingService->getUserSetting('active_dashboard')->value;
        if ($location->remoteId === $activeDashboardRemoteId) {
            return;
        }

        $this->userSettingService->setUserSetting('active_dashboard', $location->remoteId);
    }

    private function isPredefinedDashboard(Location $location): bool
    {
        $predefinedContainerRemoteId = $this->configResolver->getParameter(
            'dashboard.predefined_container_remote_id'
        );

        return (new IsPredefinedDashboard($predefinedContainerRemoteId))->isSatisfiedBy($location);
    }
}
