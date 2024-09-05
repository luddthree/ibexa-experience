<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Migrations\ProductPrice;

use Money\Money;

final class ProductCustomPrice
{
    private string $customerGroup;

    private ?Money $baseAmount;

    private ?Money $customAmount;

    /**
     * @var numeric-string|null
     */
    private ?string $customPriceRule;

    /**
     * @param numeric-string|null $customPriceRule
     */
    public function __construct(
        string $customerGroup,
        ?Money $baseAmount = null,
        ?Money $customAmount = null,
        ?string $customPriceRule = null
    ) {
        $this->customerGroup = $customerGroup;
        $this->baseAmount = $baseAmount;
        $this->customAmount = $customAmount;
        $this->customPriceRule = $customPriceRule;
    }

    public function getCustomerGroup(): string
    {
        return $this->customerGroup;
    }

    public function getBaseAmount(): ?Money
    {
        return $this->baseAmount;
    }

    public function getCustomAmount(): ?Money
    {
        return $this->customAmount;
    }

    /**
     * @return numeric-string|null
     */
    public function getCustomPriceRule(): ?string
    {
        return $this->customPriceRule;
    }
}
