<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Values;

use Ibexa\Contracts\ProductCatalog\Values\Product\ProductVariantListInterface;
use Ibexa\Contracts\ProductCatalog\Values\Product\ProductVariantQuery;

interface ProductVariantsAwareInterface
{
    public function getVariantsList(?ProductVariantQuery $query = null): ProductVariantListInterface;
}
