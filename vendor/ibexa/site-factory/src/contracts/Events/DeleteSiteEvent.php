<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\SiteFactory\Events;

use Ibexa\Contracts\Core\Repository\Event\BeforeEvent;
use Ibexa\Contracts\SiteFactory\Values\Site\Site;

final class DeleteSiteEvent extends BeforeEvent
{
    /** @var \Ibexa\Contracts\SiteFactory\Values\Site\Site */
    private $site;

    public function __construct(Site $site)
    {
        $this->site = $site;
    }

    public function getSite(): Site
    {
        return $this->site;
    }
}

class_alias(DeleteSiteEvent::class, 'EzSystems\EzPlatformSiteFactory\ServiceEvent\Events\DeleteSiteEvent');
class_alias(DeleteSiteEvent::class, 'Ibexa\SiteFactory\ServiceEvent\Events\DeleteSiteEvent');
