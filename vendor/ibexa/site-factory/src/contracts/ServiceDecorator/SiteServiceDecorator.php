<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\SiteFactory\ServiceDecorator;

use Ibexa\Contracts\SiteFactory\Service\SiteServiceInterface;
use Ibexa\Contracts\SiteFactory\Values\Site\Site;
use Ibexa\Contracts\SiteFactory\Values\Site\SiteCreateStruct;
use Ibexa\Contracts\SiteFactory\Values\Site\SiteList;
use Ibexa\Contracts\SiteFactory\Values\Site\SiteQuery;
use Ibexa\Contracts\SiteFactory\Values\Site\SiteUpdateStruct;

abstract class SiteServiceDecorator implements SiteServiceInterface
{
    /** @var \Ibexa\Contracts\SiteFactory\Service\SiteServiceInterface */
    protected $innerService;

    public function __construct(SiteServiceInterface $innerService)
    {
        $this->innerService = $innerService;
    }

    public function createSite(SiteCreateStruct $createStruct): Site
    {
        return $this->innerService->createSite($createStruct);
    }

    public function updateSite(Site $site, SiteUpdateStruct $siteUpdateStruct): Site
    {
        return $this->innerService->updateSite($site, $siteUpdateStruct);
    }

    public function deleteSite(Site $site): void
    {
        $this->innerService->deleteSite($site);
    }

    public function loadSite(int $id): Site
    {
        return $this->innerService->loadSite($id);
    }

    public function loadSites(SiteQuery $query = null): SiteList
    {
        return $this->innerService->loadSites($query);
    }

    public function countSites(SiteQuery $query = null): int
    {
        return $this->innerService->countSites($query);
    }

    public function loadPages(SiteQuery $query = null, int $offset = 0, int $limit = -1): array
    {
        return $this->innerService->loadPages($query, $offset, $limit);
    }
}

class_alias(SiteServiceDecorator::class, 'EzSystems\EzPlatformSiteFactory\ServiceDecorator\SiteServiceDecorator');
class_alias(SiteServiceDecorator::class, 'Ibexa\SiteFactory\ServiceDecorator\SiteServiceDecorator');
