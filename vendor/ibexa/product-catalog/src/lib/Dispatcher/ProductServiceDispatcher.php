<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Dispatcher;

use Ibexa\Contracts\ProductCatalog\ProductServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\LanguageSettings;
use Ibexa\Contracts\ProductCatalog\Values\Product\ProductListInterface;
use Ibexa\Contracts\ProductCatalog\Values\Product\ProductQuery;
use Ibexa\Contracts\ProductCatalog\Values\Product\ProductVariantListInterface;
use Ibexa\Contracts\ProductCatalog\Values\Product\ProductVariantQuery;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductVariantInterface;

/**
 * @extends \Ibexa\ProductCatalog\Dispatcher\AbstractServiceDispatcher<
 *     \Ibexa\Contracts\ProductCatalog\ProductServiceInterface
 * >
 */
final class ProductServiceDispatcher extends AbstractServiceDispatcher implements ProductServiceInterface
{
    public function getProduct(string $code, ?LanguageSettings $settings = null): ProductInterface
    {
        return $this->dispatch()->getProduct($code, $settings);
    }

    public function findProducts(ProductQuery $query, ?LanguageSettings $languageSettings = null): ProductListInterface
    {
        return $this->dispatch()->findProducts($query, $languageSettings);
    }

    public function getProductVariant(string $code, ?LanguageSettings $settings = null): ProductVariantInterface
    {
        return $this->dispatch()->getProductVariant($code, $settings);
    }

    public function findProductVariants(
        ProductInterface $product,
        ?ProductVariantQuery $query = null
    ): ProductVariantListInterface {
        return $this->dispatch()->findProductVariants($product, $query);
    }
}
