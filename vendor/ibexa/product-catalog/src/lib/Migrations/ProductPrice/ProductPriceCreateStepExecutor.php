<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Migrations\ProductPrice;

use Ibexa\Contracts\Migration\StepExecutor\AbstractStepExecutor;
use Ibexa\Contracts\ProductCatalog\CurrencyServiceInterface;
use Ibexa\Contracts\ProductCatalog\CustomerGroupServiceInterface;
use Ibexa\Contracts\ProductCatalog\ProductPriceServiceInterface;
use Ibexa\Contracts\ProductCatalog\ProductServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroupInterface;
use Ibexa\Contracts\ProductCatalog\Values\Price\Create\Struct\CustomerGroupPriceCreateStruct;
use Ibexa\Contracts\ProductCatalog\Values\Price\Create\Struct\ProductPriceCreateStruct;
use Ibexa\Contracts\ProductCatalog\Values\PriceInterface;
use Ibexa\Core\Base\Exceptions\NotFoundException;
use Ibexa\Migration\ValueObject\Step\StepInterface;

final class ProductPriceCreateStepExecutor extends AbstractStepExecutor
{
    private ProductServiceInterface $productService;

    private CurrencyServiceInterface $currencyService;

    private ProductPriceServiceInterface $priceService;

    private CustomerGroupServiceInterface $customerGroupService;

    public function __construct(
        ProductServiceInterface $productService,
        CurrencyServiceInterface $currencyService,
        CustomerGroupServiceInterface $customerGroupService,
        ProductPriceServiceInterface $priceService
    ) {
        $this->productService = $productService;
        $this->currencyService = $currencyService;
        $this->customerGroupService = $customerGroupService;
        $this->priceService = $priceService;
    }

    /**
     * @param \Ibexa\ProductCatalog\Migrations\ProductPrice\ProductPriceCreateStep $step
     */
    protected function doHandle(StepInterface $step): PriceInterface
    {
        $product = $this->productService->getProduct($step->getProductCode());
        $currency = $this->currencyService->getCurrencyByCode($step->getCurrencyCode());

        $createStruct = new ProductPriceCreateStruct(
            $product,
            $currency,
            $step->getAmount(),
            null,
            null
        );

        $price = $this->priceService->createProductPrice($createStruct);

        $customPriceCreateStructs = [];
        foreach ($step->getCustomPrices() as $customPrice) {
            $customerGroup = $this->customerGroupService->getCustomerGroupByIdentifier(
                $customPrice->getCustomerGroup()
            );

            if ($customerGroup === null) {
                throw new NotFoundException(CustomerGroupInterface::class, $customPrice->getCustomerGroup());
            }

            $customPriceCreateStructs[] = new CustomerGroupPriceCreateStruct(
                $customerGroup,
                $product,
                $currency,
                $customPrice->getBaseAmount() ?? $step->getAmount(),
                $customPrice->getCustomAmount(),
                $customPrice->getCustomPriceRule()
            );
        }

        $this->priceService->execute($customPriceCreateStructs);

        return $price;
    }

    public function canHandle(StepInterface $step): bool
    {
        return $step instanceof ProductPriceCreateStep;
    }
}
