<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\PageBuilder\SiteAccess;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException;
use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Core\MVC\Symfony\SiteAccess;

final class RootLocationProvider
{
    private ConfigResolverInterface $configResolver;

    private LocationService $locationService;

    public function __construct(
        ConfigResolverInterface $configResolver,
        LocationService $locationService
    ) {
        $this->configResolver = $configResolver;
        $this->locationService = $locationService;
    }

    public function getRootLocation(SiteAccess $siteAccess): ?Location
    {
        $rootLocationId = $this->configResolver->getParameter(
            'content.tree_root.location_id',
            null,
            $siteAccess->name
        );

        try {
            return $this->locationService->loadLocation($rootLocationId);
        } catch (NotFoundException | UnauthorizedException $e) {
            return null;
        }
    }
}
