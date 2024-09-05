<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Values\Price\Create\Struct;

use Ibexa\Contracts\ProductCatalog\Values\CurrencyInterface;
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroupInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Money\Money;

final class CustomerGroupPriceCreateStruct extends AbstractPriceCreateStruct
{
    private CustomerGroupInterface $customerGroup;

    /**
     * @param numeric-string|null $customPriceRule
     */
    public function __construct(
        CustomerGroupInterface $customerGroup,
        ProductInterface $product,
        CurrencyInterface $currency,
        Money $money,
        ?Money $customPriceMoney,
        ?string $customPriceRule
    ) {
        parent::__construct(
            $product,
            $currency,
            $money,
            $customPriceMoney,
            $customPriceRule
        );

        $this->customerGroup = $customerGroup;
    }

    public static function getDiscriminator(): string
    {
        return 'customer_group';
    }

    public function getCustomerGroup(): CustomerGroupInterface
    {
        return $this->customerGroup;
    }

    public function getCustomerGroupId(): int
    {
        return $this->customerGroup->getId();
    }
}
