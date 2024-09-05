<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\View\Matcher\ProductBased;

use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Ibexa\Contracts\ProductCatalog\ViewMatcher\ProductBased\ProductType as ProductTypeInterface;

final class ProductType extends AbstractProductMatcher implements ProductTypeInterface
{
    /** @var string[] */
    private array $values = [];

    protected function matchProduct(ProductInterface $product): bool
    {
        return in_array($product->getProductType()->getIdentifier(), $this->values, true);
    }

    public function setMatchingConfig($matchingConfig): void
    {
        $this->values = !is_array($matchingConfig) ? [$matchingConfig] : $matchingConfig;
    }
}
