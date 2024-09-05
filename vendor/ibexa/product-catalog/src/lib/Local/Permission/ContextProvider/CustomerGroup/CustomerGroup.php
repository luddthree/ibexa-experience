<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Permission\ContextProvider\CustomerGroup;

use Ibexa\Contracts\ProductCatalog\Permission\ContextInterface;
use Ibexa\Contracts\ProductCatalog\Permission\ContextProvider\ContextProviderInterface;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\PolicyInterface;
use Ibexa\ProductCatalog\Local\Permission\Context;
use Ibexa\ProductCatalog\Local\Repository\Values\CustomerGroup as LocalCustomerGroup;

final class CustomerGroup implements ContextProviderInterface
{
    public function accept(PolicyInterface $policy): bool
    {
        return $policy->getObject() instanceof LocalCustomerGroup;
    }

    public function getPermissionContext(PolicyInterface $policy): ContextInterface
    {
        /** @var \Ibexa\ProductCatalog\Local\Repository\Values\CustomerGroup $customerGroup */
        $customerGroup = $policy->getObject();

        return new Context($customerGroup);
    }
}
