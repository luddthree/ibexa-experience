<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\SiteFactory\Form\Data;

final class SitesDeleteData
{
    /** @var array */
    private $sites;

    /**
     * @param array $sites
     */
    public function __construct(array $sites = [])
    {
        $this->sites = $sites;
    }

    /**
     * @return array
     */
    public function getSites(): array
    {
        return $this->sites;
    }

    /**
     * @param array $sites
     */
    public function setSites(array $sites): void
    {
        $this->sites = $sites;
    }
}

class_alias(SitesDeleteData::class, 'EzSystems\EzPlatformSiteFactoryBundle\Form\Data\SitesDeleteData');
