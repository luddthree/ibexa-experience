<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\SiteFactory\Persistence\Site\Handler;

use Ibexa\Contracts\SiteFactory\Values\Site\Site;
use Ibexa\Contracts\SiteFactory\Values\Site\SiteCreateStruct;
use Ibexa\Contracts\SiteFactory\Values\Site\SiteQuery;
use Ibexa\Contracts\SiteFactory\Values\Site\SiteUpdateStruct;

interface HandlerInterface
{
    public function find(SiteQuery $query): array;

    public function count(SiteQuery $query): int;

    public function create(SiteCreateStruct $siteCreateStruct): Site;

    public function update(int $siteId, SiteUpdateStruct $siteUpdateStruct): void;

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException If site access group could not be found by $id
     */
    public function load(int $id): Site;

    public function delete(int $id): void;
}

class_alias(HandlerInterface::class, 'EzSystems\EzPlatformSiteFactory\Persistence\Site\Handler\HandlerInterface');
