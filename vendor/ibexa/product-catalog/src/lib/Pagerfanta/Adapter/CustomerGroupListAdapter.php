<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Pagerfanta\Adapter;

use Ibexa\Contracts\ProductCatalog\CustomerGroupServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroup\CustomerGroupQuery;
use Pagerfanta\Adapter\AdapterInterface;

/**
 * @implements \Pagerfanta\Adapter\AdapterInterface<\Ibexa\Contracts\ProductCatalog\Values\CustomerGroupInterface>
 */
final class CustomerGroupListAdapter implements AdapterInterface
{
    private CustomerGroupServiceInterface $customerGroupService;

    private CustomerGroupQuery $query;

    public function __construct(CustomerGroupServiceInterface $customerGroupService, ?CustomerGroupQuery $query = null)
    {
        $this->customerGroupService = $customerGroupService;
        $this->query = $query ?? new CustomerGroupQuery();
    }

    public function getNbResults(): int
    {
        $query = new CustomerGroupQuery(
            $this->query->getQuery(),
            $this->query->getSortClauses(),
            0,
        );

        return $this->customerGroupService->findCustomerGroups($query)->getTotalCount();
    }

    /**
     * @return \Ibexa\Contracts\ProductCatalog\Values\CustomerGroup\CustomerGroupListInterface
     */
    public function getSlice($offset, $length): iterable
    {
        $query = new CustomerGroupQuery(
            $this->query->getQuery(),
            $this->query->getSortClauses(),
            $length,
            $offset,
        );

        return $this->customerGroupService->findCustomerGroups($query);
    }
}
