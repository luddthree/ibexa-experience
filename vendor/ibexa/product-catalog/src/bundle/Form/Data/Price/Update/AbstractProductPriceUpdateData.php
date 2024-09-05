<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Data\Price\Update;

use Ibexa\Bundle\ProductCatalog\Form\Data\Price\AbstractProductPrice\AbstractProductPrice;
use Ibexa\Contracts\ProductCatalog\Values\CurrencyInterface;
use Ibexa\Contracts\ProductCatalog\Values\PriceInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;

abstract class AbstractProductPriceUpdateData extends AbstractProductPrice
{
    private PriceInterface $price;

    public function __construct(PriceInterface $price)
    {
        $this->price = $price;
        $this->setBasePrice($price->getBaseAmount());
    }

    public function getId(): int
    {
        return $this->price->getId();
    }

    public function getCurrency(): CurrencyInterface
    {
        return $this->price->getCurrency();
    }

    public function getPrice(): PriceInterface
    {
        return $this->price;
    }

    public function getProduct(): ProductInterface
    {
        return $this->price->getProduct();
    }
}
