<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\SiteFactory\SiteAccess;

use Ibexa\AdminUi\Siteaccess\AbstractSiteaccessPreviewVoter;

final class SiteSkeletonSiteAccessPreviewVoter extends AbstractSiteaccessPreviewVoter
{
    protected function getRootLocationIds(string $siteaccess): array
    {
        return [
            $this->configResolver->getParameter(
                'site_factory.site_skeletons_location_id',
                null,
                $siteaccess
            ),
        ];
    }
}

class_alias(SiteSkeletonSiteAccessPreviewVoter::class, 'EzSystems\EzPlatformSiteFactory\SiteAccess\SiteSkeletonSiteAccessPreviewVoter');
