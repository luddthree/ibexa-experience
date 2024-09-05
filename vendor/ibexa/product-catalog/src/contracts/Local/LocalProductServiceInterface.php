<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Local;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\ProductCatalog\Local\Values\Product\ProductCreateStruct;
use Ibexa\Contracts\ProductCatalog\Local\Values\Product\ProductUpdateStruct;
use Ibexa\Contracts\ProductCatalog\Local\Values\Product\ProductVariantUpdateStruct;
use Ibexa\Contracts\ProductCatalog\ProductServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductTypeInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductVariantInterface;

interface LocalProductServiceInterface extends ProductServiceInterface
{
    /**
     * Checks if Content is a product.
     */
    public function isProduct(Content $content): bool;

    /**
     * Returns product associated with given Content.
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function getProductFromContent(Content $content): ProductInterface;

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function createProduct(ProductCreateStruct $createStruct): ProductInterface;

    /**
     * @param iterable<\Ibexa\Contracts\ProductCatalog\Local\Values\Product\ProductVariantCreateStruct> $createStructs
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function createProductVariants(ProductInterface $product, iterable $createStructs): void;

    public function newProductCreateStruct(
        ProductTypeInterface $productType,
        string $mainLanguageCode
    ): ProductCreateStruct;

    public function newProductUpdateStruct(ProductInterface $product): ProductUpdateStruct;

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function updateProduct(ProductUpdateStruct $updateStruct): ProductInterface;

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function updateProductVariant(
        ProductVariantInterface $productVariant,
        ProductVariantUpdateStruct $updateStruct
    ): ProductVariantInterface;

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function deleteProduct(ProductInterface $product): void;

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     *
     * @return string[]
     */
    public function deleteProductVariantsByBaseProduct(ProductInterface $baseProduct): array;

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function deleteProductTranslation(ProductInterface $product, Language $language): void;
}
