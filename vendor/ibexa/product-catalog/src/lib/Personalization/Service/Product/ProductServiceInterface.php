<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Personalization\Service\Product;

use Ibexa\Contracts\ProductCatalog\Values\ProductVariantInterface;

/**
 * @internal
 */
interface ProductServiceInterface
{
    public function deleteVariant(ProductVariantInterface $productVariant): void;

    public function updateVariant(ProductVariantInterface $productVariant): void;

    /**
     * All Variants should be configured as included_item_types and available under the same SiteAccess.
     *
     * @param array<\Ibexa\Contracts\ProductCatalog\Values\ProductVariantInterface> $productVariants
     */
    public function updateVariants(array $productVariants): void;

    /**
     * All Variants should be configured as included_item_types and available under the same SiteAccess.
     *
     * @param array<\Ibexa\Contracts\ProductCatalog\Values\ProductVariantInterface> $productVariants
     */
    public function deleteVariants(array $productVariants): void;
}
