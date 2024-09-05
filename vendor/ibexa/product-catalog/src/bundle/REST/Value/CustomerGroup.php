<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Value;

use Ibexa\Contracts\ProductCatalog\Values\CustomerGroupInterface;
use Ibexa\Rest\Value;

final class CustomerGroup extends Value
{
    public ?CustomerGroupInterface $customerGroup;

    public function __construct(?CustomerGroupInterface $customerGroup)
    {
        $this->customerGroup = $customerGroup;
    }
}
