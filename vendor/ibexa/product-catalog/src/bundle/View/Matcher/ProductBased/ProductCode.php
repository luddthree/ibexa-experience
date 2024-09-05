<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\View\Matcher\ProductBased;

use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Ibexa\Contracts\ProductCatalog\ViewMatcher\ProductBased\ProductCode as ProductCodeInterface;

final class ProductCode extends AbstractProductMatcher implements ProductCodeInterface
{
    /** @var string[] */
    private array $values = [];

    protected function matchProduct(ProductInterface $product): bool
    {
        return in_array($product->getCode(), $this->values, true);
    }

    public function setMatchingConfig($matchingConfig): void
    {
        $this->values = !is_array($matchingConfig) ? [$matchingConfig] : $matchingConfig;
    }
}
