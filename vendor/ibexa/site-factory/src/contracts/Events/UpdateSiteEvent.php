<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\SiteFactory\Events;

use Ibexa\Contracts\Core\Repository\Event\AfterEvent;
use Ibexa\Contracts\SiteFactory\Values\Site\Site;
use Ibexa\Contracts\SiteFactory\Values\Site\SiteUpdateStruct;

final class UpdateSiteEvent extends AfterEvent
{
    /** @var \Ibexa\Contracts\SiteFactory\Values\Site\Site */
    private $updatedSite;

    /** @var \Ibexa\Contracts\SiteFactory\Values\Site\Site */
    private $site;

    /** @var \Ibexa\Contracts\SiteFactory\Values\Site\SiteUpdateStruct */
    private $siteUpdateStruct;

    public function __construct(
        Site $updatedSite,
        Site $site,
        SiteUpdateStruct $siteUpdateStruct
    ) {
        $this->site = $site;
        $this->siteUpdateStruct = $siteUpdateStruct;
        $this->updatedSite = $updatedSite;
    }

    public function getUpdatedSite(): Site
    {
        return $this->updatedSite;
    }

    public function getSite(): Site
    {
        return $this->site;
    }

    public function getSiteUpdateStruct(): SiteUpdateStruct
    {
        return $this->siteUpdateStruct;
    }
}

class_alias(UpdateSiteEvent::class, 'EzSystems\EzPlatformSiteFactory\ServiceEvent\Events\UpdateSiteEvent');
class_alias(UpdateSiteEvent::class, 'Ibexa\SiteFactory\ServiceEvent\Events\UpdateSiteEvent');
