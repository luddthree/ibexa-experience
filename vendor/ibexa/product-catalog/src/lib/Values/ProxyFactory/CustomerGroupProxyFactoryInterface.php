<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Values\ProxyFactory;

use Ibexa\ProductCatalog\Local\Repository\Values\CustomerGroup;

interface CustomerGroupProxyFactoryInterface
{
    /**
     * @param string[]|null $prioritizedLanguages
     */
    public function createCustomerGroupProxy(int $customerGroupId, ?array $prioritizedLanguages): CustomerGroup;
}
