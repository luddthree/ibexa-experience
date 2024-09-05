<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\SiteFactory\Form\Data;

use Ibexa\Contracts\SiteFactory\Values\Site\Site;

final class SiteDeleteData
{
    /** @var \Ibexa\Contracts\SiteFactory\Values\Site\Site|null */
    private $site;

    public function __construct(?Site $site = null)
    {
        $this->site = $site;
    }

    public function getSite(): ?Site
    {
        return $this->site;
    }

    public function setSite(?Site $site)
    {
        $this->site = $site;
    }
}

class_alias(SiteDeleteData::class, 'EzSystems\EzPlatformSiteFactoryBundle\Form\Data\SiteDeleteData');
