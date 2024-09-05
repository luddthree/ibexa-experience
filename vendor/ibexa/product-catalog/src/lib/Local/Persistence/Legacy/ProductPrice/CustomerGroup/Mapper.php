<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Legacy\ProductPrice\CustomerGroup;

use Ibexa\Contracts\ProductCatalog\Values\Price\Create\Struct\CustomerGroupPriceCreateStruct;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\AbstractDoctrineDatabase;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\CustomerGroup\HandlerInterface;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\ProductPrice\Inheritance\MapperInterface;
use Ibexa\ProductCatalog\Local\Persistence\Values\Currency;
use Ibexa\ProductCatalog\Local\Persistence\Values\ProductPrice\CustomerGroupPrice as SpiCustomerGroupPrice;
use Webmozart\Assert\Assert;

final class Mapper implements MapperInterface
{
    private HandlerInterface $customerGroupHandler;

    public function __construct(HandlerInterface $customerGroupHandler)
    {
        $this->customerGroupHandler = $customerGroupHandler;
    }

    public function canHandleResultSet(string $discriminator): bool
    {
        return $discriminator === CustomerGroupPriceCreateStruct::getDiscriminator();
    }

    public function handleResultSet(string $discriminator, array $row, Currency $currency): SpiCustomerGroupPrice
    {
        $customerGroupId = $row[$discriminator . AbstractDoctrineDatabase::DISCRIMINATOR_SEPARATOR . 'customer_group_id'];
        Assert::integer($customerGroupId);
        $customerGroup = $this->customerGroupHandler->find($customerGroupId);

        return new SpiCustomerGroupPrice(
            $row['id'],
            $row['amount'],
            $currency,
            $row['product_code'],
            $row['custom_price_amount'],
            $row['custom_price_rule'],
            $customerGroup,
        );
    }
}
