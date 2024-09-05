<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\UI;

use Ibexa\Contracts\ProductCatalog\Values\CustomerGroupInterface;
use Ibexa\Contracts\ProductCatalog\Values\CustomPriceAwareInterface;

final class CustomPrice
{
    private CustomerGroupInterface $customerGroup;

    private ?CustomPriceAwareInterface $price;

    public function __construct(CustomerGroupInterface $customerGroup, ?CustomPriceAwareInterface $price)
    {
        $this->customerGroup = $customerGroup;
        $this->price = $price;
    }

    public function getCustomerGroup(): CustomerGroupInterface
    {
        return $this->customerGroup;
    }

    public function getPrice(): ?CustomPriceAwareInterface
    {
        return $this->price;
    }
}
