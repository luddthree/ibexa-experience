<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\SiteFactory\Service;

use Ibexa\Contracts\SiteFactory\Values\Site\Site;
use Ibexa\Contracts\SiteFactory\Values\Site\SiteCreateStruct;
use Ibexa\Contracts\SiteFactory\Values\Site\SiteList;
use Ibexa\Contracts\SiteFactory\Values\Site\SiteQuery;
use Ibexa\Contracts\SiteFactory\Values\Site\SiteUpdateStruct;

interface SiteServiceInterface
{
    public function createSite(SiteCreateStruct $createStruct): Site;

    public function updateSite(Site $site, SiteUpdateStruct $siteUpdateStruct): Site;

    public function deleteSite(Site $site): void;

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    public function loadSite(int $id): Site;

    public function loadSites(SiteQuery $query = null): SiteList;

    public function countSites(SiteQuery $query = null): int;

    public function loadPages(SiteQuery $query = null, int $offset = 0, int $limit = -1): array;
}

class_alias(SiteServiceInterface::class, 'EzSystems\EzPlatformSiteFactory\Service\SiteServiceInterface');
