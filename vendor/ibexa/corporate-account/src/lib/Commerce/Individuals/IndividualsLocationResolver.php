<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Commerce\Individuals;

use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\Repository\Values\User\UserGroup;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;

final class IndividualsLocationResolver implements IndividualsLocationResolverInterface
{
    private LocationService $locationService;

    private UserService $userService;

    private ConfigResolverInterface $configResolver;

    public function __construct(
        LocationService $locationService,
        UserService $userService,
        ConfigResolverInterface $configResolver
    ) {
        $this->locationService = $locationService;
        $this->userService = $userService;
        $this->configResolver = $configResolver;
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function resolveLocation(): Location
    {
        return $this->resolveLegacyLocation() ?? $this->getRegistrationUserGroup()->contentInfo->getMainLocation();
    }

    private function resolveLegacyLocation(): ?Location
    {
        $locationId = $this->getPrivateCustomersUserGroupLocationId();
        if ($locationId !== null) {
            return $this->locationService->loadLocation($locationId);
        }

        return null;
    }

    private function getPrivateCustomersUserGroupLocationId(): ?int
    {
        $parameter = 'user_group_location.private';
        $namespace = 'ibexa.commerce.site_access.config.core';

        if ($this->configResolver->hasParameter($parameter, $namespace)) {
            return (int)$this->configResolver->getParameter($parameter, $namespace);
        }

        return null;
    }

    private function getRegistrationUserGroup(): UserGroup
    {
        return $this->userService->loadUserGroup(
            (int)$this->configResolver->getParameter('user_registration.group_id')
        );
    }
}
