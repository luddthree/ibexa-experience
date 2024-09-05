<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\SiteFactory\Events;

use Ibexa\Contracts\Core\Repository\Event\BeforeEvent;
use Ibexa\Contracts\SiteFactory\Values\Site\Site;
use Ibexa\Contracts\SiteFactory\Values\Site\SiteCreateStruct;
use UnexpectedValueException;

final class BeforeCreateSiteEvent extends BeforeEvent
{
    /** @var \Ibexa\Contracts\SiteFactory\Values\Site\SiteCreateStruct */
    private $siteCreateStruct;

    /** @var \Ibexa\Contracts\SiteFactory\Values\Site\Site */
    private $site;

    public function __construct(SiteCreateStruct $siteCreateStruct)
    {
        $this->siteCreateStruct = $siteCreateStruct;
    }

    public function getSiteCreateStruct(): SiteCreateStruct
    {
        return $this->siteCreateStruct;
    }

    public function getSite(): Site
    {
        if (!$this->hasSite()) {
            throw new UnexpectedValueException(sprintf('Return value is not set or not of type %s. Check hasSite() or set it using setSite() before you call the getter.', Site::class));
        }

        return $this->site;
    }

    public function setSite(?Site $site): void
    {
        $this->site = $site;
    }

    public function hasSite(): bool
    {
        return $this->site instanceof Site;
    }
}

class_alias(BeforeCreateSiteEvent::class, 'EzSystems\EzPlatformSiteFactory\ServiceEvent\Events\BeforeCreateSiteEvent');
class_alias(BeforeCreateSiteEvent::class, 'Ibexa\SiteFactory\ServiceEvent\Events\BeforeCreateSiteEvent');
