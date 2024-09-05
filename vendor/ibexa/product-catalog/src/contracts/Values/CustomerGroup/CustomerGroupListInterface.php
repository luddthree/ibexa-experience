<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Values\CustomerGroup;

use Countable;
use IteratorAggregate;

/**
 * @extends IteratorAggregate<\Ibexa\Contracts\ProductCatalog\Values\CustomerGroupInterface>
 */
interface CustomerGroupListInterface extends IteratorAggregate, Countable
{
    /**
     * Partial list of customer groups.
     *
     * @return \Ibexa\Contracts\ProductCatalog\Values\CustomerGroupInterface[]
     */
    public function getCustomerGroups(): array;

    /**
     * Return total count of found customer groups (filtered by permissions).
     */
    public function getTotalCount(): int;
}
