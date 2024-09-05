<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\SiteFactory\Values\Site;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;

/**
 * @property-read int $totalCount
 * @property-read \Ibexa\Contracts\SiteFactory\Values\Site\Site[] $sites
 */
final class SiteList extends ValueObject
{
    /**
     * The total count of found sites.
     *
     * @var int
     */
    protected $totalCount;

    /** @var \Ibexa\Contracts\SiteFactory\Values\Site\Site[] */
    protected $sites = [];

    /** @var string[] */
    protected $pages = [];

    public function __construct(int $totalCount = 0, array $sites = [], array $pages = [])
    {
        parent::__construct();

        $this->totalCount = $totalCount;
        $this->sites = $sites;
        $this->pages = $pages;
    }

    public function getSites(): array
    {
        return $this->sites;
    }

    public function getPages(): array
    {
        return $this->pages;
    }
}

class_alias(SiteList::class, 'EzSystems\EzPlatformSiteFactory\Values\Site\SiteList');
