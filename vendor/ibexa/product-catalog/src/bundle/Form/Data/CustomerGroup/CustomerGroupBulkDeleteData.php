<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Data\CustomerGroup;

final class CustomerGroupBulkDeleteData
{
    /** @var \Ibexa\Contracts\ProductCatalog\Values\CustomerGroupInterface[] */
    private array $customerGroups;

    /**
     * @param \Ibexa\Contracts\ProductCatalog\Values\CustomerGroupInterface[] $products
     */
    public function __construct(array $products = [])
    {
        $this->customerGroups = $products;
    }

    /**
     * @return \Ibexa\Contracts\ProductCatalog\Values\CustomerGroupInterface[]
     */
    public function getCustomerGroups(): array
    {
        return $this->customerGroups;
    }

    /**
     * @param \Ibexa\Contracts\ProductCatalog\Values\CustomerGroupInterface[] $customerGroups
     */
    public function setCustomerGroups(array $customerGroups): void
    {
        $this->customerGroups = $customerGroups;
    }
}
