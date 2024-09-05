<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\REST;

use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\ProductCatalog\CurrencyServiceInterface;
use Ibexa\Contracts\ProductCatalog\CustomerGroupServiceInterface;
use Ibexa\Contracts\ProductCatalog\ProductPriceServiceInterface;
use Ibexa\Contracts\ProductCatalog\ProductServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\PriceInterface;

final class CustomPriceUpdateWebTest extends AbstractPriceRestWebTestCase
{
    public function testPriceUpdate(): void
    {
        $price = $this->getCustomPrice('0001', 'EUR', 'customer_group_1');

        $this->assertClientRequest(
            'PATCH',
            '/api/ibexa/v2/product/catalog/products/0001/prices/' . $price->getId(),
            [
                'CONTENT_TYPE' => 'application/vnd.ibexa.api.CustomPriceUpdateStruct+json',
                'HTTP_ACCEPT' => 'application/vnd.ibexa.api.CustomPrice+json',
            ],
            <<<JSON
            {
              "CustomPriceUpdateStruct": {
                "amount": 250,
                "customAmount": 170,
                "currency": "EUR"
              }
            }
            JSON
        );
    }

    protected function getResourceType(): ?string
    {
        return 'CustomPrice';
    }

    private function getCustomPrice(string $code, string $currencyCode, string $customerGroupIdentifier): PriceInterface
    {
        $repository = $this->getContainer()->get(Repository::class);

        return $repository->sudo(function () use ($code, $currencyCode, $customerGroupIdentifier): PriceInterface {
            $currencyService = $this->getContainer()->get(CurrencyServiceInterface::class);
            $customerGroupService = $this->getContainer()->get(CustomerGroupServiceInterface::class);
            $productService = $this->getContainer()->get(ProductServiceInterface::class);
            $productPriceService = $this->getContainer()->get(ProductPriceServiceInterface::class);

            $basePrice = $productPriceService->getPriceByProductAndCurrency(
                $productService->getProduct($code),
                $currencyService->getCurrencyByCode($currencyCode)
            );

            return $productPriceService->findOneForCustomerGroup(
                $basePrice,
                $customerGroupService->getCustomerGroupByIdentifier($customerGroupIdentifier)
            );
        });
    }
}
