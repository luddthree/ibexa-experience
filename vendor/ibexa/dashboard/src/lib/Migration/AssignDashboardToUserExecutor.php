<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Dashboard\Migration;

use Ibexa\Contracts\Core\Persistence\User\Handler;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Core\Repository\Values\User\UserReference;
use Ibexa\Migration\StepExecutor\ActionExecutor\ExecutorInterface;
use Ibexa\Migration\ValueObject\Step\Action;
use Ibexa\User\UserSetting\UserSettingService;

final class AssignDashboardToUserExecutor implements ExecutorInterface
{
    private Repository $repository;

    private PermissionResolver $permissionResolver;

    private UserSettingService $settingService;

    private Handler $userHandler;

    public function __construct(
        Repository $repository,
        PermissionResolver $permissionResolver,
        UserSettingService $settingService,
        Handler $userHandler
    ) {
        $this->repository = $repository;
        $this->permissionResolver = $permissionResolver;
        $this->settingService = $settingService;
        $this->userHandler = $userHandler;
    }

    public function handle(Action $action, ValueObject $valueObject): void
    {
        $login = $action->getValue();

        /** @var \Ibexa\Core\Repository\Values\Content\Content $valueObject */
        $mainLocation = $valueObject->getVersionInfo()->getContentInfo()->getMainLocation();
        if ($mainLocation === null || $mainLocation->getParentLocation() === null) {
            return;
        }
        $userDashboardsRemoteLocationId = $mainLocation->getParentLocation()->remoteId;
        $activeDashboardRemoteLocationId = $mainLocation->remoteId;

        $userReference = $this->permissionResolver->getCurrentUserReference();

        $this->repository->sudo(
            function () use ($login, $userDashboardsRemoteLocationId, $activeDashboardRemoteLocationId, $userReference): void {
                $this->permissionResolver->setCurrentUserReference(
                    new UserReference($this->userHandler->loadByLogin($login)->id)
                );
                try {
                    $this->settingService->setUserSetting('user_dashboards', $userDashboardsRemoteLocationId);
                    $this->settingService->setUserSetting('active_dashboard', $activeDashboardRemoteLocationId);
                } finally {
                    $this->permissionResolver->setCurrentUserReference($userReference);
                }
            }
        );
    }
}
