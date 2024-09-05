<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\Search\Common\IndexDataProvider;

use Ibexa\Contracts\Core\Persistence\Content as SPIContent;
use Ibexa\Contracts\Core\Persistence\Content\Field;
use Ibexa\Contracts\Core\Search;
use Ibexa\ProductCatalog\FieldType\ProductSpecification\Type;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\CustomerGroup\Handler as CustomerGroupHandler;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\ProductPrice\Handler as ProductPriceHandler;
use Ibexa\ProductCatalog\Local\Persistence\Values\AbstractProductPrice;
use Ibexa\ProductCatalog\Local\Persistence\Values\CustomerGroup;
use Ibexa\ProductCatalog\Local\Persistence\Values\ProductPrice\CustomerGroupPrice;
use Ibexa\ProductCatalog\Local\Repository\Search\Common\FieldNameBuilder\PriceFieldNameBuilder;
use Ibexa\ProductCatalog\Money\DecimalMoneyFactory;
use Money\Currency;
use Money\Money;
use Money\Parser\DecimalMoneyParser;

final class PriceIndexDataProvider extends AbstractFieldTypeIndexDataProvider
{
    private ProductPriceHandler $priceHandler;

    private CustomerGroupHandler $customerGroupHandler;

    private DecimalMoneyFactory $decimalMoneyFactory;

    public function __construct(
        ProductPriceHandler $priceHandler,
        CustomerGroupHandler $customerGroupHandler,
        DecimalMoneyFactory $decimalMoneyFactory
    ) {
        $this->priceHandler = $priceHandler;
        $this->customerGroupHandler = $customerGroupHandler;
        $this->decimalMoneyFactory = $decimalMoneyFactory;
    }

    protected function doGetSearchData(SPIContent $content, Field $field): array
    {
        $productCode = $field->value->externalData['code'];

        $fields = [];
        foreach ($this->getPrices($productCode) as $name => $price) {
            $fields[] = new Search\Field(
                $name,
                (int)$price->getAmount(),
                new Search\FieldType\IntegerField()
            );
        }

        return $fields;
    }

    /**
     * @return array<string,\Money\Money>
     */
    private function getPrices(string $productCode): array
    {
        $prices = [];
        $parser = $this->decimalMoneyFactory->getMoneyParser();

        $currencies = [];
        foreach ($this->loadPriceForProduct($productCode) as $price) {
            $prices[$this->getFieldNameForPrice($price)] = $this->getAmount($parser, $price);

            if (!in_array($price->getCurrency()->code, $currencies, true)) {
                $currencies[] = $price->getCurrency()->code;
            }
        }

        $groups = $this->customerGroupHandler->findAll();
        foreach ($currencies as $currency) {
            $mainContextIdentifier = $this->getFieldNameForMainPrice($currency);
            if (!array_key_exists($mainContextIdentifier, $prices)) {
                continue;
            }

            $mainPrice = $prices[$mainContextIdentifier];
            foreach ($groups as $group) {
                $groupContextIdentifier = $this->getFieldNameForCustomPrice($currency, $group);
                if (array_key_exists($groupContextIdentifier, $prices)) {
                    continue;
                }

                $price = $mainPrice;
                if ($group->hasGlobalPriceRate()) {
                    $price = $this->applyGlobalPriceRate($mainPrice, $group->globalPriceRate);
                }

                $prices[$groupContextIdentifier] = $price;
            }
        }

        return $prices;
    }

    /**
     * @param numeric-string $rate
     */
    private function applyGlobalPriceRate(Money $price, string $rate): Money
    {
        return $price->add($price->multiply($rate)->divide(100));
    }

    /**
     * @return \Ibexa\ProductCatalog\Local\Persistence\Values\AbstractProductPrice[]
     */
    private function loadPriceForProduct(string $productCode): array
    {
        return $this->priceHandler->findBy([
            'product_code' => $productCode,
        ]);
    }

    private function getAmount(DecimalMoneyParser $parser, AbstractProductPrice $price): Money
    {
        $amount = $price->getAmount();
        if ($price instanceof CustomerGroupPrice) {
            $amount = $price->getCustomPriceAmount() ?? $price->getAmount();
        }

        return $parser->parse($amount, new Currency($price->getCurrency()->code));
    }

    private function getFieldNameForMainPrice(string $currencyCode): string
    {
        return (new PriceFieldNameBuilder($currencyCode))->build();
    }

    private function getFieldNameForCustomPrice(string $currencyCode, CustomerGroup $customerGroup): string
    {
        return (new PriceFieldNameBuilder($currencyCode))
            ->withCustomerGroup($customerGroup->identifier)
            ->build();
    }

    private function getFieldNameForPrice(AbstractProductPrice $price): string
    {
        $builder = new PriceFieldNameBuilder($price->getCurrency()->code);
        if ($price instanceof CustomerGroupPrice) {
            $builder->withCustomerGroup($price->getCustomerGroup()->identifier);
        }

        return $builder->build();
    }

    protected function getFieldTypeIdentifier(): string
    {
        return Type::FIELD_TYPE_IDENTIFIER;
    }
}
