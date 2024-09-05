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

interface ProductPriceServiceInterface
{
    /**
     * @param iterable<\Ibexa\Contracts\ProductCatalog\Values\Price\ProductPriceStructInterface> $structs
     */
    public function execute(iterable $structs): void;

    public function deletePrice(ProductPriceDeleteStructInterface $struct): void;

    public function findPrices(?PriceQuery $query = null): PriceListInterface;

    public function findPricesByProductCode(string $code): PriceListInterface;

    public function createProductPrice(ProductPriceCreateStructInterface $struct): PriceInterface;

    public function updateProductPrice(ProductPriceUpdateStructInterface $struct): PriceInterface;

    public function findOneForCustomerGroup(PriceInterface $price, CustomerGroupInterface $customerGroup): ?CustomPriceAwareInterface;

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    public function getPriceByProductAndCurrency(ProductInterface $product, CurrencyInterface $currency): PriceInterface;

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    public function getPriceById(int $id): PriceInterface;
}
