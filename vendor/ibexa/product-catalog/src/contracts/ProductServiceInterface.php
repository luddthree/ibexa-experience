<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog;

use Ibexa\Contracts\ProductCatalog\Values\LanguageSettings;
use Ibexa\Contracts\ProductCatalog\Values\Product\ProductListInterface;
use Ibexa\Contracts\ProductCatalog\Values\Product\ProductQuery;
use Ibexa\Contracts\ProductCatalog\Values\Product\ProductVariantListInterface;
use Ibexa\Contracts\ProductCatalog\Values\Product\ProductVariantQuery;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductVariantInterface;

interface ProductServiceInterface
{
    /**
     * Loads product with given code.
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException if the user is not allowed to read the product
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException if the product with the given code does not exist
     */
    public function getProduct(string $code, ?LanguageSettings $settings = null): ProductInterface;

    public function findProducts(ProductQuery $query, ?LanguageSettings $languageSettings = null): ProductListInterface;

    /**
     * Loads product variant with given code.
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException if the user is not allowed to read the product
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException if the product with the given code does not exist
     */
    public function getProductVariant(string $code, ?LanguageSettings $settings = null): ProductVariantInterface;

    public function findProductVariants(
        ProductInterface $product,
        ?ProductVariantQuery $query = null
    ): ProductVariantListInterface;
}
