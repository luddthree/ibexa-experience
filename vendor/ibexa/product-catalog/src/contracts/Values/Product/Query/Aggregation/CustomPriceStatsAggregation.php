<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Values\Product\Query\Aggregation;

use Ibexa\Contracts\ProductCatalog\Values\CurrencyInterface;
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroupInterface;

final class CustomPriceStatsAggregation extends AbstractPriceStatsAggregation
{
    private ?CustomerGroupInterface $customerGroup;

    public function __construct(string $name, CurrencyInterface $currency, ?CustomerGroupInterface $customerGroup = null)
    {
        parent::__construct($name, $currency);

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
