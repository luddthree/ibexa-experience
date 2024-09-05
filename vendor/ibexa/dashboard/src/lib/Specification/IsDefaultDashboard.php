<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Dashboard\Specification;

use Ibexa\Contracts\Core\Exception\InvalidArgumentException;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\Core\Specification\AbstractSpecification;

final class IsDefaultDashboard extends AbstractSpecification
{
    public const DEFAULT_DASHBOARD_PARAM_NAME = 'dashboard.default_dashboard_remote_id';

    private ConfigResolverInterface $configResolver;

    public function __construct(ConfigResolverInterface $configResolver)
    {
        $this->configResolver = $configResolver;
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Location $item
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function isSatisfiedBy($item): bool
    {
        if (!$item instanceof Location) {
            throw new InvalidArgumentException(
                '$item',
                sprintf('Must be an instance of %s', Location::class)
            );
        }

        return $item->remoteId === $this->configResolver->getParameter(self::DEFAULT_DASHBOARD_PARAM_NAME);
    }
}
