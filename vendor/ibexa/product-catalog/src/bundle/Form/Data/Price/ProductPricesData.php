<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Data\Price;

use IteratorAggregate;
use Traversable;

/**
 * @implements \IteratorAggregate<int, \Ibexa\Bundle\ProductCatalog\Form\Data\Price\ProductPriceDataInterface>
 */
final class ProductPricesData implements IteratorAggregate
{
    /**
     * @var \Ibexa\Bundle\ProductCatalog\Form\Data\Price\CustomerGroupAwareInterface[]
     */
    private array $customerGroupPrices;

    private ProductPriceDataInterface $price;

    /**
     * @param array<\Ibexa\Bundle\ProductCatalog\Form\Data\Price\CustomerGroupAwareInterface> $customerGroupPrices
     */
    public function __construct(ProductPriceDataInterface $priceData, array $customerGroupPrices)
    {
        $this->price = $priceData;
        $this->customerGroupPrices = $customerGroupPrices;
    }

    public function getPrice(): ProductPriceDataInterface
    {
        return $this->price;
    }

    /**
     * @return \Ibexa\Bundle\ProductCatalog\Form\Data\Price\CustomerGroupAwareInterface[]
     */
    public function getCustomerGroupPrices(): array
    {
        return $this->customerGroupPrices;
    }

    public function addCustomerGroupPrice(CustomerGroupAwareInterface $data): void
    {
        if (in_array($data, $this->customerGroupPrices, true)) {
            return;
        }

        $this->customerGroupPrices[] = $data;
    }

    public function removeCustomerGroupPrice(CustomerGroupAwareInterface $data): void
    {
        foreach ($this->customerGroupPrices as $key => $customerGroupPrice) {
            if ($customerGroupPrice === $data) {
                unset($this->customerGroupPrices[$key]);
            }
        }

        $this->customerGroupPrices = array_values($this->customerGroupPrices);
    }

    public function getIterator(): Traversable
    {
        yield $this->price;
        yield from $this->customerGroupPrices;
    }
}
