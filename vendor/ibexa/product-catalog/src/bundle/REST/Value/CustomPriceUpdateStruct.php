<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Value;

use Ibexa\Contracts\ProductCatalog\Values\Price\Update\Struct\ProductPriceUpdateStruct;
use Ibexa\Contracts\ProductCatalog\Values\Price\Update\Struct\ProductPriceUpdateStructInterface as PriceUpdateStructInterface;
use Ibexa\Contracts\ProductCatalog\Values\PriceInterface;
use Money\Money;

final class CustomPriceUpdateStruct extends AbstractPriceUpdateStruct
{
    public ?Money $customerPriceMoney;

    public ?string $customerPriceRule;

    public function __construct(?Money $money, ?Money $customerPriceMoney, ?string $customerPriceRule)
    {
        parent::__construct($money);

        $this->customerPriceMoney = $customerPriceMoney;
        $this->customerPriceRule = $customerPriceRule;
    }

    public function toUpdateStruct(PriceInterface $price): PriceUpdateStructInterface
    {
        return new ProductPriceUpdateStruct(
            $price,
            $this->money,
            $this->customerPriceMoney,
            $this->customerPriceRule
        );
    }
}
