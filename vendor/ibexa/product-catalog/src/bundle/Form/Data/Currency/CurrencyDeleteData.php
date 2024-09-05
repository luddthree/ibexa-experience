<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Data\Currency;

final class CurrencyDeleteData
{
    /** @var \Ibexa\Contracts\ProductCatalog\Values\CurrencyInterface[] */
    private array $currencies;

    /**
     * @param \Ibexa\Contracts\ProductCatalog\Values\CurrencyInterface[] $currencies
     */
    public function __construct(array $currencies = [])
    {
        $this->currencies = $currencies;
    }

    /**
     * @return \Ibexa\Contracts\ProductCatalog\Values\CurrencyInterface[]
     */
    public function getCurrencies(): array
    {
        return $this->currencies;
    }

    /**
     * @param \Ibexa\Contracts\ProductCatalog\Values\CurrencyInterface[] $currencies
     */
    public function setCurrencies(array $currencies): void
    {
        $this->currencies = $currencies;
    }
}
