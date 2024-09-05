<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\SiteFactory\Events;

use Ibexa\Contracts\Core\Repository\Event\AfterEvent;
use Ibexa\Contracts\SiteFactory\Values\Site\Site;
use Ibexa\Contracts\SiteFactory\Values\Site\SiteCreateStruct;

final class CreateSiteEvent extends AfterEvent
{
    /** @var \Ibexa\Contracts\SiteFactory\Values\Site\Site */
    private $site;

    /** @var \Ibexa\Contracts\SiteFactory\Values\Site\SiteCreateStruct */
    private $siteCreateStruct;

    public function __construct(
        Site $site,
        SiteCreateStruct $siteCreateStruct
    ) {
        $this->site = $site;
        $this->siteCreateStruct = $siteCreateStruct;
    }

    public function getSite(): Site
    {
        return $this->site;
    }

    public function getSiteCreateStruct(): SiteCreateStruct
    {
        return $this->siteCreateStruct;
    }
}

class_alias(CreateSiteEvent::class, 'EzSystems\EzPlatformSiteFactory\ServiceEvent\Events\CreateSiteEvent');
class_alias(CreateSiteEvent::class, 'Ibexa\SiteFactory\ServiceEvent\Events\CreateSiteEvent');
