<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\SiteFactory\Persistence\PublicAccess\Handler;

use Ibexa\Contracts\SiteFactory\Values\Site\PublicAccess;
use Ibexa\Contracts\SiteFactory\Values\Site\SiteQuery;

interface HandlerInterface
{
    public function load(string $identifier): ?PublicAccess;

    public function find(SiteQuery $query): array;

    public function match(string $host): array;
}

class_alias(HandlerInterface::class, 'EzSystems\EzPlatformSiteFactory\Persistence\PublicAccess\Handler\HandlerInterface');
