<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Value;

use Ibexa\Contracts\ProductCatalog\Values\CustomerGroup\CustomerGroupListInterface;
use Ibexa\Rest\Value;

final class CustomerGroupView extends Value
{
    private string $identifier;

    private CustomerGroupListInterface $customerGroupList;

    public function __construct(string $identifier, CustomerGroupListInterface $customerGroupList)
    {
        $this->identifier = $identifier;
        $this->customerGroupList = $customerGroupList;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getCustomerGroupList(): CustomerGroupListInterface
    {
        return $this->customerGroupList;
    }
}
