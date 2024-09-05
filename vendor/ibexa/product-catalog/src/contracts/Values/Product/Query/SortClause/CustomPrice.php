<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Values\Product\Query\SortClause;

use Ibexa\Contracts\ProductCatalog\Values\CurrencyInterface;
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroupInterface;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\SortClause;

final class CustomPrice extends SortClause
{
    private CurrencyInterface $currency;

    private ?CustomerGroupInterface $customerGroup;

    public function __construct(
        CurrencyInterface $currency,
        string $sortDirection = self::SORT_ASC,
        ?CustomerGroupInterface $customerGroup = null
    ) {
        parent::__construct($sortDirection);

        $this->currency = $currency;
        $this->customerGroup = $customerGroup;
    }

    public function getCurrency(): CurrencyInterface
    {
        return $this->currency;
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
