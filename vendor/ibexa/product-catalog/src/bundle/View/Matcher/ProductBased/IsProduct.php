<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\View\Matcher\ProductBased;

use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Ibexa\Contracts\ProductCatalog\ViewMatcher\ProductBased\IsProduct as IsProductInterface;

final class IsProduct extends AbstractProductMatcher implements IsProductInterface
{
    protected function matchProduct(ProductInterface $product): bool
    {
        return true;
    }

    public function setMatchingConfig($matchingConfig): void
    {
        /* There is no configuration for this matcher */
    }
}
