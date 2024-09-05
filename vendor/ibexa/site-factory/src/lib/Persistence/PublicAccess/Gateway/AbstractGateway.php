<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\SiteFactory\Persistence\PublicAccess\Gateway;

use Ibexa\Contracts\SiteFactory\Values\Query\Criterion;

abstract class AbstractGateway
{
    abstract public function loadPublicAccessData(string $identifier): ?array;

    abstract public function find(
        Criterion $criterion,
        int $offset = 0,
        int $limit = -1,
        bool $doCount = true
    ): array;

    abstract public function match(string $host): array;
}

class_alias(AbstractGateway::class, 'EzSystems\EzPlatformSiteFactory\Persistence\PublicAccess\Gateway\AbstractGateway');
