<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\SiteFactory\Persistence\Site\Gateway;

use Ibexa\Contracts\SiteFactory\Values\Query\Criterion;
use Ibexa\Contracts\SiteFactory\Values\Site\SiteCreateStruct;
use Ibexa\Contracts\SiteFactory\Values\Site\SiteUpdateStruct;

abstract class AbstractGateway
{
    abstract public function find(Criterion $criterion, int $offset, int $limit, bool $doCount = true): array;

    abstract public function count(Criterion $criterion): int;

    abstract public function insert(SiteCreateStruct $siteCreateStruct): int;

    abstract public function loadSiteData(int $id): array;

    abstract public function insertSite(SiteCreateStruct $siteCreateStruct): int;

    abstract public function deleteSite(int $id): void;

    abstract public function canDeleteSite(int $id): bool;

    abstract public function update(int $siteId, SiteUpdateStruct $siteUpdateStruct): void;
}

class_alias(AbstractGateway::class, 'EzSystems\EzPlatformSiteFactory\Persistence\Site\Gateway\AbstractGateway');
