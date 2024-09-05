<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\EventSubscriber;

use Ibexa\Bundle\AdminUi\IbexaAdminUiBundle;
use Ibexa\Core\MVC\Symfony\SiteAccess;
use Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessServiceInterface;
use Ibexa\Core\MVC\Symfony\SiteAccessGroup;

trait SiteAccessServiceMockTrait
{
    /**
     * @param \Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessServiceInterface|\PHPUnit\Framework\MockObject\MockObject $siteAccessServiceMock
     */
    private function configureSiteAccessServiceMock(
        bool $isAdminSiteAccess,
        SiteAccessServiceInterface $siteAccessServiceMock
    ): SiteAccessServiceInterface {
        $siteAccess = new SiteAccess('siteaccess');
        if ($isAdminSiteAccess) {
            $siteAccess->groups = [
                new SiteAccessGroup(IbexaAdminUiBundle::ADMIN_GROUP_NAME),
            ];
        }

        $siteAccessServiceMock->method('getCurrent')->willReturn($siteAccess);
        $siteAccessServiceMock->method('get')->willReturn($siteAccess);

        return $siteAccessServiceMock;
    }
}
