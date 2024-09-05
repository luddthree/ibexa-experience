<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Specification;

use Ibexa\AdminUi\Specification\AbstractSpecification;
use Ibexa\Bundle\CorporateAccount\IbexaCorporateAccountBundle;
use Ibexa\Contracts\Core\Exception\InvalidArgumentException;
use Ibexa\Core\MVC\Symfony\SiteAccess;
use Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessServiceInterface;

final class IsCorporate extends AbstractSpecification
{
    private SiteAccessServiceInterface $siteAccessService;

    public function __construct(SiteAccessServiceInterface $siteAccessService)
    {
        $this->siteAccessService = $siteAccessService;
    }

    /**
     * @param \Ibexa\Core\MVC\Symfony\SiteAccess $item
     */
    public function isSatisfiedBy($item): bool
    {
        if (!$item instanceof SiteAccess) {
            throw new InvalidArgumentException('$item', sprintf('Must be an instance of %s', SiteAccess::class));
        }

        // Workaround: for some reason value returned from SiteAccessServiceInterface::getCurrent() doesn't
        // contain groups
        $currentSiteAccess = $this->siteAccessService->get($item->name);
        foreach ($currentSiteAccess->groups as $group) {
            if ($group->getName() === IbexaCorporateAccountBundle::CUSTOMER_PORTAL_GROUP_NAME) {
                return true;
            }
        }

        return false;
    }
}
