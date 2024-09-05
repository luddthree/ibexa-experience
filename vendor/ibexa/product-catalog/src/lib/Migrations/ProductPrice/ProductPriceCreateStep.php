<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Migrations\ProductPrice;

use Ibexa\Migration\ValueObject\Step\StepInterface;
use Money\Money;

final class ProductPriceCreateStep implements StepInterface
{
    private string $productCode;

    private Money $amount;

    private string $currencyCode;

    /** @var \Ibexa\ProductCatalog\Migrations\ProductPrice\ProductCustomPrice[] */
    private array $customPrices;

    /**
     * @param \Ibexa\ProductCatalog\Migrations\ProductPrice\ProductCustomPrice[] $customPrices
     */
    public function __construct(
        string $productCode,
        Money $amount,
        string $currencyCode,
        array $customPrices = []
    ) {
        $this->productCode = $productCode;
        $this->amount = $amount;
        $this->currencyCode = $currencyCode;
        $this->customPrices = $customPrices;
    }

    public function getProductCode(): string
    {
        return $this->productCode;
    }

    public function getAmount(): Money
    {
        return $this->amount;
    }

    public function getCurrencyCode(): string
    {
        return $this->currencyCode;
    }

    /**
     * @return \Ibexa\ProductCatalog\Migrations\ProductPrice\ProductCustomPrice[]
     */
    public function getCustomPrices(): array
    {
        return $this->customPrices;
    }
}
