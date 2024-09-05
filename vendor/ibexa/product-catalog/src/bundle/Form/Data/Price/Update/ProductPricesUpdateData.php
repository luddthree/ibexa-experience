<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Data\Price\Update;

final class ProductPricesUpdateData
{
    /**
     * @var \Ibexa\Bundle\ProductCatalog\Form\Data\Price\Update\CustomerGroupPriceUpdateData[]
     */
    private array $customerGroupPrices;

    private ProductPriceUpdateData $price;

    /**
     * @param array<\Ibexa\Bundle\ProductCatalog\Form\Data\Price\Update\CustomerGroupPriceUpdateData> $customerGroupPrices
     */
    public function __construct(ProductPriceUpdateData $priceUpdateData, array $customerGroupPrices)
    {
        $this->price = $priceUpdateData;
        $this->customerGroupPrices = $customerGroupPrices;
    }

    public function getPrice(): ProductPriceUpdateData
    {
        return $this->price;
    }

    /**
     * @return \Ibexa\Bundle\ProductCatalog\Form\Data\Price\Update\CustomerGroupPriceUpdateData[]
     */
    public function getCustomerGroupPrices(): array
    {
        return $this->customerGroupPrices;
    }
}
