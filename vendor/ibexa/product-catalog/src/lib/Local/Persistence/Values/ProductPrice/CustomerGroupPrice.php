<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Values\ProductPrice;

use Ibexa\ProductCatalog\Local\Persistence\Values\AbstractProductPrice;
use Ibexa\ProductCatalog\Local\Persistence\Values\Currency;
use Ibexa\ProductCatalog\Local\Persistence\Values\CustomerGroup;

final class CustomerGroupPrice extends AbstractProductPrice
{
    private CustomerGroup $customerGroup;

    /** @var numeric-string|null */
    private ?string $customPriceAmount;

    /** @var numeric-string|null */
    private ?string $customPriceRule;

    /**
     * @param numeric-string|null $customPriceAmount
     * @param numeric-string|null $customPriceRule
     */
    public function __construct(
        int $id,
        string $amount,
        Currency $currency,
        string $productCode,
        ?string $customPriceAmount,
        ?string $customPriceRule,
        CustomerGroup $customerGroup
    ) {
        parent::__construct($id, $amount, $currency, $productCode);

        $this->customPriceAmount = $customPriceAmount;
        $this->customPriceRule = $customPriceRule;
        $this->customerGroup = $customerGroup;
    }

    public function getCustomerGroup(): CustomerGroup
    {
        return $this->customerGroup;
    }

    /**
     * @return numeric-string|null
     */
    public function getCustomPriceAmount(): ?string
    {
        return $this->customPriceAmount;
    }

    /**
     * @return numeric-string|null
     */
    public function getCustomPriceRule(): ?string
    {
        return $this->customPriceRule;
    }
}
