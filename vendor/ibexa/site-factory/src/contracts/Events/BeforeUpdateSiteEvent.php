<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\SiteFactory\Events;

use Ibexa\Contracts\Core\Repository\Event\BeforeEvent;
use Ibexa\Contracts\SiteFactory\Values\Site\Site;
use Ibexa\Contracts\SiteFactory\Values\Site\SiteUpdateStruct;
use UnexpectedValueException;

final class BeforeUpdateSiteEvent extends BeforeEvent
{
    /** @var \Ibexa\Contracts\SiteFactory\Values\Site\Site */
    private $site;

    /** @var \Ibexa\Contracts\SiteFactory\Values\Site\SiteUpdateStruct */
    private $siteUpdateStruct;

    /** @var \Ibexa\Contracts\SiteFactory\Values\Site\Site|null */
    private $updatedSite;

    public function __construct(
        Site $site,
        SiteUpdateStruct $siteUpdateStruct
    ) {
        $this->siteUpdateStruct = $siteUpdateStruct;
        $this->site = $site;
    }

    public function getSiteUpdateStruct(): SiteUpdateStruct
    {
        return $this->siteUpdateStruct;
    }

    public function getSite(): Site
    {
        return $this->site;
    }

    public function getUpdatedSite(): Site
    {
        if (!$this->hasUpdatedSite()) {
            throw new UnexpectedValueException(sprintf('Return value is not set or not of type %s. Check hasSite() or set it using setSite() before you call the getter.', Site::class));
        }

        return $this->updatedSite;
    }

    public function setSite(?Site $updatedSite): void
    {
        $this->updatedSite = $updatedSite;
    }

    public function hasUpdatedSite(): bool
    {
        return $this->updatedSite instanceof Site;
    }
}

class_alias(BeforeUpdateSiteEvent::class, 'EzSystems\EzPlatformSiteFactory\ServiceEvent\Events\BeforeUpdateSiteEvent');
class_alias(BeforeUpdateSiteEvent::class, 'Ibexa\SiteFactory\ServiceEvent\Events\BeforeUpdateSiteEvent');
