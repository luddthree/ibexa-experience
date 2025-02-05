<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\DataMapper\ProductPrice\Create;

use Ibexa\Bundle\ProductCatalog\Form\Data\Price\Create\CustomerGroupPriceCreateData;
use Ibexa\Bundle\ProductCatalog\Form\Data\Price\ProductPriceDataInterface;
use Ibexa\Bundle\ProductCatalog\Form\DataMapper\ProductPrice\DataToStructTransformerInterface;
use Ibexa\Contracts\ProductCatalog\Values\Price\Create\Struct\CustomerGroupPriceCreateStruct;
use Ibexa\ProductCatalog\Money\DecimalMoneyFactory;
use Money\Currency;

final class CustomerGroupDataToStructTransformer implements DataToStructTransformerInterface
{
    private DecimalMoneyFactory $decimalMoneyParserFactory;

    public function __construct(DecimalMoneyFactory $decimalMoneyParserFactory)
    {
        $this->decimalMoneyParserFactory = $decimalMoneyParserFactory;
    }

    public function convertDataToStruct(ProductPriceDataInterface $priceData): CustomerGroupPriceCreateStruct
    {
        assert($priceData instanceof CustomerGroupPriceCreateData);

        $customerGroup = $priceData->getCustomerGroup();

        $amount = $priceData->getBasePrice();
        assert($amount !== null);
        $currency = $priceData->getCurrency();

        $parser = $this->decimalMoneyParserFactory->getMoneyParser();
        $moneyCurrency = new Currency($currency->getCode());
        $money = $parser->parse($amount, $moneyCurrency);

        $customPrice = $priceData->getCustomPrice();
        $customPriceMoney = $customPrice === null
            ? null
            : $parser->parse($customPrice, $moneyCurrency);

        return new CustomerGroupPriceCreateStruct(
            $customerGroup,
            $priceData->getProduct(),
            $currency,
            $money,
            $customPriceMoney,
            $priceData->getCustomPriceRule()
        );
    }

    public function supports(ProductPriceDataInterface $priceData): bool
    {
        return $priceData instanceof CustomerGroupPriceCreateData;
    }
}
