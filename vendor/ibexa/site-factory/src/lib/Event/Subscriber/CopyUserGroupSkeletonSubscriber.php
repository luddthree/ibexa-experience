<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\SiteFactory\Event\Subscriber;

use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\RoleService;
use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\SiteFactory\Event\CopySkeletonEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class CopyUserGroupSkeletonSubscriber implements EventSubscriberInterface
{
    /** @var \Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface */
    private $configResolver;

    /** @var \Ibexa\Contracts\Core\Repository\LocationService */
    private $locationService;

    /** @var \Ibexa\Contracts\Core\Repository\RoleService */
    private $roleService;

    /** @var \Ibexa\Contracts\Core\Repository\UserService */
    private $userService;

    public function __construct(
        ConfigResolverInterface $configResolver,
        LocationService $locationService,
        RoleService $roleService,
        UserService $userService
    ) {
        $this->configResolver = $configResolver;
        $this->locationService = $locationService;
        $this->roleService = $roleService;
        $this->userService = $userService;
    }

    public static function getSubscribedEvents(): array
    {
        return [CopySkeletonEvent::class => ['copyUserGroupSkeleton']];
    }

    public function copyUserGroupSkeleton(CopySkeletonEvent $event): void
    {
        $userGroupSkeletonIds = $event->getSiteCreateStruct()->userGroupSkeletonIds;
        if (empty($userGroupSkeletonIds)) {
            return;
        }

        $usersLocation = $this->locationService->loadLocation(
            $this->configResolver->getParameter('location_ids.users')
        );

        foreach ($userGroupSkeletonIds as $userGroupLocationId) {
            $userGroupLocation = $this->locationService->loadLocation($userGroupLocationId);
            $copiedUserGroupLocation = $this->locationService->copySubtree($userGroupLocation, $usersLocation);
            $this->rewriteRoles($userGroupLocation, $copiedUserGroupLocation);
        }
    }

    private function rewriteRoles(Location $userGroupLocation, Location $copiedUserGroupLocation): void
    {
        $userGroup = $this->userService->loadUserGroup($userGroupLocation->contentId);
        $roleAssignments = $this->roleService->getRoleAssignmentsForUserGroup($userGroup);
        $copiedUserGroup = $this->userService->loadUserGroup($copiedUserGroupLocation->contentId);

        foreach ($roleAssignments as $roleAssignment) {
            $this->roleService->assignRoleToUserGroup(
                $roleAssignment->role,
                $copiedUserGroup,
                $roleAssignment->limitation
            );
        }
    }
}

class_alias(CopyUserGroupSkeletonSubscriber::class, 'EzSystems\EzPlatformSiteFactory\Event\Subscriber\CopyUserGroupSkeletonSubscriber');
