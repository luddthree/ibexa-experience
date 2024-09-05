<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\Values;

use ArrayIterator;
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroup\CustomerGroupListInterface;
use Traversable;

final class CustomerGroupList implements CustomerGroupListInterface
{
    /** @var \Ibexa\Contracts\ProductCatalog\Values\CustomerGroupInterface[] */
    private array $customerGroups;

    private int $totalCount;

    /**
     * @param \Ibexa\Contracts\ProductCatalog\Values\CustomerGroupInterface[] $customerGroups
     */
    public function __construct(array $customerGroups = [], int $total = 0)
    {
        $this->customerGroups = $customerGroups;
        $this->totalCount = $total;
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->customerGroups);
    }

    public function count(): int
    {
        return count($this->customerGroups);
    }

    public function getCustomerGroups(): array
    {
        return $this->customerGroups;
    }

    public function getTotalCount(): int
    {
        return $this->totalCount;
    }
}
