<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Legacy\ProductPrice\CustomerGroup;

use Ibexa\Contracts\ProductCatalog\Values\CurrencyInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\CustomerGroup\DomainMapperInterface as CustomerGroupDomainMapperInterface;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\ProductPrice\Inheritance\DomainMapperInterface;
use Ibexa\ProductCatalog\Local\Persistence\Values\AbstractProductPrice;
use Ibexa\ProductCatalog\Local\Persistence\Values\ProductPrice\CustomerGroupPrice as SpiCustomerGroupPrice;
use Ibexa\ProductCatalog\Local\Repository\Values\Price\CustomGroupPrice;
use Money\Money;
use Money\MoneyFormatter;
use Money\MoneyParser;

final class DomainMapper implements DomainMapperInterface
{
    private CustomerGroupDomainMapperInterface $customerGroupMapper;

    public function __construct(CustomerGroupDomainMapperInterface $customerGroupMapper)
    {
        $this->customerGroupMapper = $customerGroupMapper;
    }

    public function canMapSpiPrice(AbstractProductPrice $spiPrice): bool
    {
        return $spiPrice instanceof SpiCustomerGroupPrice;
    }

    public function mapSpiPrice(
        MoneyFormatter $moneyFormatter,
        MoneyParser $moneyParser,
        ProductInterface $product,
        CurrencyInterface $currency,
        AbstractProductPrice $spiPrice,
        Money $money
    ): CustomGroupPrice {
        assert($spiPrice instanceof SpiCustomerGroupPrice);
        $spiCustomerGroup = $spiPrice->getCustomerGroup();
        $customerGroup = $this->customerGroupMapper->createFromSpi($spiCustomerGroup);
        $customPriceMoney = $spiPrice->getCustomPriceAmount() === null
            ? null
            : $moneyParser->parse($spiPrice->getCustomPriceAmount(), $money->getCurrency());

        return new CustomGroupPrice(
            $moneyFormatter,
            $spiPrice->getId(),
            $product,
            $currency,
            $money,
            $customPriceMoney,
            $spiPrice->getCustomPriceRule(),
            $customerGroup
        );
    }
}
