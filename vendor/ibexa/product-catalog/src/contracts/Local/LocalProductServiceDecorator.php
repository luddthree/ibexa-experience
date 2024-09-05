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
use Ibexa\Contracts\ProductCatalog\Values\LanguageSettings;
use Ibexa\Contracts\ProductCatalog\Values\Product\ProductListInterface;
use Ibexa\Contracts\ProductCatalog\Values\Product\ProductQuery;
use Ibexa\Contracts\ProductCatalog\Values\Product\ProductVariantListInterface;
use Ibexa\Contracts\ProductCatalog\Values\Product\ProductVariantQuery;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductTypeInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductVariantInterface;

abstract class LocalProductServiceDecorator implements LocalProductServiceInterface
{
    protected LocalProductServiceInterface $innerService;

    public function __construct(LocalProductServiceInterface $innerService)
    {
        $this->innerService = $innerService;
    }

    public function createProduct(ProductCreateStruct $createStruct): ProductInterface
    {
        return $this->innerService->createProduct($createStruct);
    }

    /**
     * @param iterable<\Ibexa\Contracts\ProductCatalog\Local\Values\Product\ProductVariantCreateStruct> $createStructs
     */
    public function createProductVariants(ProductInterface $product, iterable $createStructs): void
    {
        $this->innerService->createProductVariants($product, $createStructs);
    }

    public function newProductCreateStruct(
        ProductTypeInterface $productType,
        string $mainLanguageCode
    ): ProductCreateStruct {
        return $this->innerService->newProductCreateStruct($productType, $mainLanguageCode);
    }

    public function newProductUpdateStruct(ProductInterface $product): ProductUpdateStruct
    {
        return $this->innerService->newProductUpdateStruct($product);
    }

    public function updateProduct(ProductUpdateStruct $updateStruct): ProductInterface
    {
        return $this->innerService->updateProduct($updateStruct);
    }

    public function updateProductVariant(
        ProductVariantInterface $productVariant,
        ProductVariantUpdateStruct $updateStruct
    ): ProductVariantInterface {
        return $this->innerService->updateProductVariant($productVariant, $updateStruct);
    }

    public function deleteProduct(ProductInterface $product): void
    {
        $this->innerService->deleteProduct($product);
    }

    public function deleteProductVariantsByBaseProduct(ProductInterface $baseProduct): array
    {
        return $this->innerService->deleteProductVariantsByBaseProduct($baseProduct);
    }

    public function deleteProductTranslation(ProductInterface $product, Language $language): void
    {
        $this->innerService->deleteProductTranslation($product, $language);
    }

    public function getProduct(string $code, ?LanguageSettings $settings = null): ProductInterface
    {
        return $this->innerService->getProduct($code, $settings);
    }

    public function getProductFromContent(Content $content): ProductInterface
    {
        return $this->innerService->getProductFromContent($content);
    }

    public function isProduct(Content $content): bool
    {
        return $this->innerService->isProduct($content);
    }

    public function findProducts(ProductQuery $query, ?LanguageSettings $languageSettings = null): ProductListInterface
    {
        return $this->innerService->findProducts($query, $languageSettings);
    }

    public function getProductVariant(string $code, ?LanguageSettings $settings = null): ProductVariantInterface
    {
        return $this->innerService->getProductVariant($code, $settings);
    }

    public function findProductVariants(
        ProductInterface $product,
        ?ProductVariantQuery $query = null
    ): ProductVariantListInterface {
        return $this->innerService->findProductVariants($product, $query);
    }
}
