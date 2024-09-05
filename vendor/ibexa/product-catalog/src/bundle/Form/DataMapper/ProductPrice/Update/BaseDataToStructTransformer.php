<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\DataMapper\ProductPrice\Update;

use Ibexa\Bundle\ProductCatalog\Form\Data\Price\ProductPriceDataInterface;
use Ibexa\Bundle\ProductCatalog\Form\Data\Price\Update\AbstractProductPriceUpdateData;
use Ibexa\Bundle\ProductCatalog\Form\DataMapper\ProductPrice\DataToStructTransformerInterface;
use Ibexa\Contracts\ProductCatalog\Values\Price\Update\Struct\ProductPriceUpdateStruct;
use Ibexa\Contracts\ProductCatalog\Values\Price\Update\Struct\ProductPriceUpdateStructInterface;
use Ibexa\ProductCatalog\Money\DecimalMoneyFactory;
use Money\Currency;

final class BaseDataToStructTransformer implements DataToStructTransformerInterface
{
    private DecimalMoneyFactory $decimalMoneyParserFactory;

    public function __construct(DecimalMoneyFactory $decimalMoneyParserFactory)
    {
        $this->decimalMoneyParserFactory = $decimalMoneyParserFactory;
    }

    public function convertDataToStruct(ProductPriceDataInterface $priceData): ProductPriceUpdateStructInterface
    {
        assert($priceData instanceof AbstractProductPriceUpdateData);

        $currency = $priceData->getCurrency();
        $moneyCurrency = new Currency($currency->getCode());
        $basePrice = $priceData->getBasePrice();
        $customPrice = $priceData->getCustomPrice();

        if ($basePrice !== null) {
            $basePrice = $this->decimalMoneyParserFactory->getMoneyParser()->parse($basePrice, $moneyCurrency);
        }

        if ($customPrice !== null) {
            $customPrice = $this->decimalMoneyParserFactory->getMoneyParser()->parse($customPrice, $moneyCurrency);
        }

        return new ProductPriceUpdateStruct(
            $priceData->getPrice(),
            $basePrice,
            $customPrice,
            $priceData->getCustomPriceRule()
        );
    }

    public function supports(ProductPriceDataInterface $priceData): bool
    {
        return $priceData instanceof AbstractProductPriceUpdateData;
    }
}
