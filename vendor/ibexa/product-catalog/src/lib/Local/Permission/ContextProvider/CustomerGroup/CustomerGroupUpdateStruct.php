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
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroup\CustomerGroupUpdateStruct as CustomerGroupUpdateStructValue;
use Ibexa\ProductCatalog\Local\Permission\Context;

final class CustomerGroupUpdateStruct implements ContextProviderInterface
{
    public function accept(PolicyInterface $policy): bool
    {
        return $policy->getObject() instanceof CustomerGroupUpdateStructValue;
    }

    public function getPermissionContext(PolicyInterface $policy): ContextInterface
    {
        /** @var \Ibexa\Contracts\ProductCatalog\Values\CustomerGroup\CustomerGroupUpdateStruct $customerGroupUpdateStruct */
        $customerGroupUpdateStruct = $policy->getObject();

        return new Context($customerGroupUpdateStruct);
    }
}
