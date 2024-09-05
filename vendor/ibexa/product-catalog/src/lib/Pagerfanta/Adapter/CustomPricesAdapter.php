<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Pagerfanta\Adapter;

use Ibexa\Bundle\ProductCatalog\UI\CustomPrice;
use Ibexa\Contracts\ProductCatalog\ProductPriceServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\PriceInterface;
use Pagerfanta\Adapter\AdapterInterface;

/**
 * @template-extends \Ibexa\ProductCatalog\Pagerfanta\Adapter\DecoratorAdapter<
 *      \Ibexa\Contracts\ProductCatalog\Values\CustomerGroupInterface,
 *      \Ibexa\Bundle\ProductCatalog\UI\CustomPrice
 * >
 */
final class CustomPricesAdapter extends DecoratorAdapter
{
    private ProductPriceServiceInterface $priceService;

    private PriceInterface $mainPrice;

    /**
     * @param \Pagerfanta\Adapter\AdapterInterface<\Ibexa\Contracts\ProductCatalog\Values\CustomerGroupInterface> $innerAdapter
     */
    public function __construct(
        AdapterInterface $innerAdapter,
        ProductPriceServiceInterface $priceService,
        PriceInterface $mainPrice
    ) {
        parent::__construct($innerAdapter);

        $this->priceService = $priceService;
        $this->mainPrice = $mainPrice;
    }

    /**
     * @param \Ibexa\Contracts\ProductCatalog\Values\CustomerGroupInterface $value
     */
    protected function decorate($value): CustomPrice
    {
        return new CustomPrice(
            $value,
            $this->priceService->findOneForCustomerGroup($this->mainPrice, $value)
        );
    }
}
