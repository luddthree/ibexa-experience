<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Values\Price;

use Ibexa\Contracts\ProductCatalog\ProductPriceServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;

interface ProductPriceStructInterface
{
    public function execute(ProductPriceServiceInterface $productPriceService): void;

    public function getProduct(): ProductInterface;
}
