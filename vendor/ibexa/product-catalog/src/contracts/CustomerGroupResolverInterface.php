<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog;

use Ibexa\Contracts\Core\Repository\Values\User\User;
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroupInterface;

interface CustomerGroupResolverInterface
{
    /**
     * Resolves customer group for current/given user.
     */
    public function resolveCustomerGroup(?User $user = null): ?CustomerGroupInterface;
}
