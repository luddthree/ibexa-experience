<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Values;

use Money\Money;

interface CustomPriceAwareInterface extends PriceInterface
{
    public function getCustomPrice(): ?Money;

    /**
     * @return numeric-string|null
     */
    public function getCustomPriceAmount(): ?string;

    /**
     * @return numeric-string|null
     */
    public function getCustomPriceRule(): ?string;
}
