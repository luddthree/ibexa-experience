<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Permission\ContextProvider;

use Ibexa\Contracts\ProductCatalog\Permission\ContextInterface;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\PolicyInterface;

interface ContextProviderInterface
{
    public function accept(PolicyInterface $policy): bool;

    public function getPermissionContext(PolicyInterface $policy): ContextInterface;
}
