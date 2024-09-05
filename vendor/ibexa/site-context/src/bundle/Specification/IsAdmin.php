<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\SiteContext\Specification;

use Ibexa\Bundle\AdminUi\IbexaAdminUiBundle;
use Ibexa\Contracts\Core\Exception\InvalidArgumentType;
use Ibexa\Contracts\Core\Specification\AbstractSpecification;
use Ibexa\Core\MVC\Symfony\SiteAccess;

/**
 * Returns true if current siteaccess is administrative.
 */
final class IsAdmin extends AbstractSpecification
{
    /**
     * @param \Ibexa\Core\MVC\Symfony\SiteAccess $item
     *
     * @throws \Ibexa\Contracts\Core\Exception\InvalidArgumentType
     */
    public function isSatisfiedBy($item): bool
    {
        if (!$item instanceof SiteAccess) {
            throw new InvalidArgumentType('$item', SiteAccess::class);
        }

        foreach ($item->groups as $group) {
            if ($group->getName() === IbexaAdminUiBundle::ADMIN_GROUP_NAME) {
                return true;
            }
        }

        return false;
    }
}
