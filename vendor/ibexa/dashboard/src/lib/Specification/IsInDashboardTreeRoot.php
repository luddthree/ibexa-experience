<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Dashboard\Specification;

use Ibexa\Contracts\Core\Exception\InvalidArgumentException;
use Ibexa\Contracts\Core\Persistence\Content\Location\Handler;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\Core\Specification\AbstractSpecification;

final class IsInDashboardTreeRoot extends AbstractSpecification
{
    public const DASHBOARD_CONTAINER_LOCATION_REMOTE_ID = 'dashboard.container_remote_id';

    private ConfigResolverInterface $configResolver;

    private Handler $locationHandler;

    public function __construct(
        ConfigResolverInterface $configResolver,
        Handler $locationHandler
    ) {
        $this->configResolver = $configResolver;
        $this->locationHandler = $locationHandler;
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Location $item
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Exception\InvalidArgumentException
     */
    public function isSatisfiedBy($item): bool
    {
        if (!$item instanceof Location) {
            throw new InvalidArgumentException(
                '$item',
                sprintf('Must be an instance of %s', Location::class)
            );
        }

        $dashboardRootLocation = $this->locationHandler->loadByRemoteId(
            $this->configResolver->getParameter(self::DASHBOARD_CONTAINER_LOCATION_REMOTE_ID)
        );

        return str_starts_with($item->getPathString(), $dashboardRootLocation->pathString);
    }
}
