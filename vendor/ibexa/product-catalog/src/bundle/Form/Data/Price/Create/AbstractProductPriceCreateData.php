<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Data\Price\Create;

use Ibexa\Bundle\ProductCatalog\Form\Data\Price\AbstractProductPrice\AbstractProductPrice;
use Ibexa\Contracts\ProductCatalog\Values\CurrencyInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;

abstract class AbstractProductPriceCreateData extends AbstractProductPrice
{
    private ProductInterface $product;

    private CurrencyInterface $currency;

    public function __construct(ProductInterface $product, CurrencyInterface $currency)
    {
        $this->product = $product;
        $this->currency = $currency;
    }

    public function getProduct(): ProductInterface
    {
        return $this->product;
    }

    public function getCurrency(): CurrencyInterface
    {
        return $this->currency;
    }
}
