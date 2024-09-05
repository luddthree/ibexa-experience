<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Values\Price\Update\Struct;

use Ibexa\Contracts\ProductCatalog\Values\Price\ProductPriceStructInterface;
use Ibexa\Contracts\ProductCatalog\Values\PriceInterface;
use Money\Money;

interface ProductPriceUpdateStructInterface extends ProductPriceStructInterface
{
    public function getPrice(): PriceInterface;

    /**
     * @deprecated since 4.2. Use getPrice()->getId() instead.
     */
    public function getId(): int;

    public function getMoney(): ?Money;

    public function getCustomPriceMoney(): ?Money;

    public function getCustomPriceRule(): ?string;
}
