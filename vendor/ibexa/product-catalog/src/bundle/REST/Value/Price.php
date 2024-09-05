<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Value;

use Ibexa\Contracts\ProductCatalog\Values\PriceInterface;
use Ibexa\Rest\Value;

final class Price extends Value
{
    public PriceInterface $price;

    public function __construct(PriceInterface $price)
    {
        $this->price = $price;
    }
}
