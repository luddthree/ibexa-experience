<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\HttpCache\UserContext;

use FOS\HttpCache\UserContext\ContextProvider;
use FOS\HttpCache\UserContext\UserContext;
use Ibexa\Contracts\ProductCatalog\CustomerGroupResolverInterface;

final class CustomerGroupContextProvider implements ContextProvider
{
    private CustomerGroupResolverInterface $customerGroupResolver;

    public function __construct(CustomerGroupResolverInterface $customerGroupResolver)
    {
        $this->customerGroupResolver = $customerGroupResolver;
    }

    public function updateUserContext(UserContext $context): void
    {
        $customerGroup = $this->customerGroupResolver->resolveCustomerGroup();
        if ($customerGroup !== null) {
            $context->addParameter('customer_group', $customerGroup->getIdentifier());
        }
    }
}
