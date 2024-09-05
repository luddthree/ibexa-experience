<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog;

use Ibexa\Contracts\ProductCatalog\Values\CurrencyInterface;
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroupInterface;
use Ibexa\Contracts\ProductCatalog\Values\CustomPriceAwareInterface;
use Ibexa\Contracts\ProductCatalog\Values\Price\Create\Struct\ProductPriceCreateStructInterface;
use Ibexa\Contracts\ProductCatalog\Values\Price\Delete\Struct\ProductPriceDeleteStructInterface;
use Ibexa\Contracts\ProductCatalog\Values\Price\PriceListInterface;
use Ibexa\Contracts\ProductCatalog\Values\Price\PriceQuery;
use Ibexa\Contracts\ProductCatalog\Values\Price\Update\Struct\ProductPriceUpdateStructInterface;
use Ibexa\Contracts\ProductCatalog\Values\PriceInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;

abstract class ProductPriceServiceDecorator implements ProductPriceServiceInterface
{
    protected ProductPriceServiceInterface $innerService;

    public function __construct(ProductPriceServiceInterface $innerService)
    {
        $this->innerService = $innerService;
    }

    public function execute(iterable $structs): void
    {
        $this->innerService->execute($structs);
    }

    public function deletePrice(ProductPriceDeleteStructInterface $struct): void
    {
        $this->innerService->deletePrice($struct);
    }

    public function findPricesByProductCode(string $code): PriceListInterface
    {
        return $this->innerService->findPricesByProductCode($code);
    }

    public function createProductPrice(ProductPriceCreateStructInterface $struct): PriceInterface
    {
        return $this->innerService->createProductPrice($struct);
    }

    public function updateProductPrice(ProductPriceUpdateStructInterface $struct): PriceInterface
    {
        return $this->innerService->updateProductPrice($struct);
    }

    public function findOneForCustomerGroup(
        PriceInterface $price,
        CustomerGroupInterface $customerGroup
    ): ?CustomPriceAwareInterface {
        return $this->innerService->findOneForCustomerGroup($price, $customerGroup);
    }

    public function getPriceByProductAndCurrency(
        ProductInterface $product,
        CurrencyInterface $currency
    ): PriceInterface {
        return $this->innerService->getPriceByProductAndCurrency($product, $currency);
    }

    public function getPriceById(int $id): PriceInterface
    {
        return $this->innerService->getPriceById($id);
    }

    public function findPrices(?PriceQuery $query = null): PriceListInterface
    {
        return $this->innerService->findPrices($query);
    }
}
