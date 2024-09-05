<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog;

use Ibexa\Contracts\ProductCatalog\Permission\Policy\PolicyInterface;

interface PermissionResolverInterface
{
    public function canUser(
        PolicyInterface $policy
    ): bool;

    /**
     * @throws \Ibexa\ProductCatalog\Exception\UnauthorizedException
     */
    public function assertPolicy(
        PolicyInterface $policy
    ): void;
}
