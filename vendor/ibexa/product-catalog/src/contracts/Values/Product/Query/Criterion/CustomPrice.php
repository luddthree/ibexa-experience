<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion;

use Ibexa\Contracts\ProductCatalog\Values\CustomerGroupInterface;
use Money\Money;

final class CustomPrice extends AbstractPriceCriterion
{
    private ?CustomerGroupInterface $customerGroup;

    public function __construct(
        Money $value,
        string $operator = Operator::EQ,
        ?CustomerGroupInterface $customerGroup = null
    ) {
        parent::__construct($value, $operator);

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
