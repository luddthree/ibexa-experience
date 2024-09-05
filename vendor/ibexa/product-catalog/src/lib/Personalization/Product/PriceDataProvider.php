<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Personalization\Product;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\ProductCatalog\ProductPriceServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\ContentAwareProductInterface;
use Ibexa\Contracts\ProductCatalog\Values\CurrencyInterface;
use Ibexa\Contracts\ProductCatalog\Values\PriceInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;

/**
 *  @phpstan-type T array{
 *     amount: int,
 *     currency: string
 * }
 */
final class PriceDataProvider implements DataProviderInterface
{
    private const DATA_KEY = 'product_base_price';

    private ProductPriceServiceInterface $priceService;

    public function __construct(
        ProductPriceServiceInterface $priceService
    ) {
        $this->priceService = $priceService;
    }

    public function getData(ContentAwareProductInterface $product, string $languageCode): ?array
    {
        $data = null;
        $currency = $this->resolveCurrency($product);
        if ($currency !== null) {
            $basePrice = $this->getBasePrice($product, $currency);
            if ($basePrice !== null) {
                $data = [
                    self::DATA_KEY => [
                        'amount' => $basePrice->getAmount(),
                        'currency' => $basePrice->getCurrency()->getCode(),
                    ],
                ];
            }
        }

        return $data;
    }

    private function resolveCurrency(ProductInterface $product): ?CurrencyInterface
    {
        $priceList = $this->priceService->findPricesByProductCode($product->getCode());
        if ($priceList->getTotalCount() === 0) {
            return null;
        }

        return $priceList->getPrices()[0]->getCurrency();
    }

    private function getBasePrice(ProductInterface $product, CurrencyInterface $currency): ?PriceInterface
    {
        try {
            return $this->priceService->getPriceByProductAndCurrency($product, $currency);
        } catch (NotFoundException $e) {
            return null;
        }
    }
}
