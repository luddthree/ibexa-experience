<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Data;

use Ibexa\Contracts\ProductCatalog\Values\CustomerGroupInterface;
use Symfony\Component\Validator\Constraints as Assert;

final class CustomerGroupDeleteData
{
    /**
     * @Assert\NotBlank()
     */
    private ?CustomerGroupInterface $customerGroup;

    public function __construct(?CustomerGroupInterface $customerGroup = null)
    {
        $this->customerGroup = $customerGroup;
    }

    public function getCustomerGroup(): ?CustomerGroupInterface
    {
        return $this->customerGroup;
    }

    public function setCustomerGroup(?CustomerGroupInterface $customerGroup): void
    {
        $this->customerGroup = $customerGroup;
    }
}
