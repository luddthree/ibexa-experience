<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\REST;

use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\ProductCatalog\CurrencyServiceInterface;
use Ibexa\Contracts\ProductCatalog\ProductPriceServiceInterface;
use Ibexa\Contracts\ProductCatalog\ProductServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\PriceInterface;

final class PriceUpdateWebTest extends AbstractPriceRestWebTestCase
{
    public function testPriceUpdate(): void
    {
        $price = $this->getPrice('0001', 'EUR');

        $this->assertClientRequest(
            'PATCH',
            '/api/ibexa/v2/product/catalog/products/0001/prices/' . $price->getId(),
            [
                'CONTENT_TYPE' => 'application/vnd.ibexa.api.PriceUpdateStruct+json',
                'HTTP_ACCEPT' => 'application/vnd.ibexa.api.Price+json',
            ],
            <<<JSON
            {
              "PriceUpdateStruct": {
                "amount": 6000,
                "currency": "USD"
              }
            }
            JSON
        );
    }

    protected function getResourceType(): ?string
    {
        return 'Price';
    }

    private function getPrice(string $code, string $currencyCode): PriceInterface
    {
        $repository = $this->getContainer()->get(Repository::class);

        return $repository->sudo(function () use ($code, $currencyCode): PriceInterface {
            $currencyService = $this->getContainer()->get(CurrencyServiceInterface::class);
            $productService = $this->getContainer()->get(ProductServiceInterface::class);
            $productPriceService = $this->getContainer()->get(ProductPriceServiceInterface::class);

            return $productPriceService->getPriceByProductAndCurrency(
                $productService->getProduct($code),
                $currencyService->getCurrencyByCode($currencyCode)
            );
        });
    }
}
