<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\SiteFactory\Event;

use Ibexa\Contracts\SiteFactory\Values\Site\SiteCreateStruct;
use Symfony\Contracts\EventDispatcher\Event;

final class CopySkeletonEvent extends Event
{
    /** @var \Ibexa\Contracts\SiteFactory\Values\Site\SiteCreateStruct */
    private $siteCreateStruct;

    public function __construct(SiteCreateStruct $siteCreateStruct)
    {
        $this->siteCreateStruct = $siteCreateStruct;
    }

    public function getSiteCreateStruct(): SiteCreateStruct
    {
        return $this->siteCreateStruct;
    }
}

class_alias(CopySkeletonEvent::class, 'EzSystems\EzPlatformSiteFactory\Event\CopySkeletonEvent');
