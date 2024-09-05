<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Local;

use Ibexa\Contracts\ProductCatalog\Local\Values\ProductType\ProductTypeCreateStruct;
use Ibexa\Contracts\ProductCatalog\Local\Values\ProductType\ProductTypeUpdateStruct;
use Ibexa\Contracts\ProductCatalog\Values\LanguageSettings;
use Ibexa\Contracts\ProductCatalog\Values\ProductType\ProductTypeListInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductType\ProductTypeQuery;
use Ibexa\Contracts\ProductCatalog\Values\ProductTypeInterface;

abstract class LocalProductTypeServiceDecorator implements LocalProductTypeServiceInterface
{
    protected LocalProductTypeServiceInterface $innerService;

    public function __construct(LocalProductTypeServiceInterface $innerService)
    {
        $this->innerService = $innerService;
    }

    public function createProductType(ProductTypeCreateStruct $createStruct): ProductTypeInterface
    {
        return $this->innerService->createProductType($createStruct);
    }

    public function newProductTypeCreateStruct(string $identifier, string $mainLanguageCode): ProductTypeCreateStruct
    {
        return $this->innerService->newProductTypeCreateStruct($identifier, $mainLanguageCode);
    }

    public function newProductTypeUpdateStruct(ProductTypeInterface $productType): ProductTypeUpdateStruct
    {
        return $this->innerService->newProductTypeUpdateStruct($productType);
    }

    public function updateProductType(ProductTypeUpdateStruct $updateStruct): ProductTypeInterface
    {
        return $this->innerService->updateProductType($updateStruct);
    }

    public function deleteProductType(ProductTypeInterface $productType): void
    {
        $this->innerService->deleteProductType($productType);
    }

    public function deleteProductTypeTranslation(ProductTypeInterface $productType, string $languageCode): void
    {
        $this->innerService->deleteProductTypeTranslation($productType, $languageCode);
    }

    public function isProductTypeUsed(ProductTypeInterface $productType): bool
    {
        return $this->innerService->isProductTypeUsed($productType);
    }

    public function getProductType(
        string $identifier,
        ?LanguageSettings $languageSettings = null
    ): ProductTypeInterface {
        return $this->innerService->getProductType($identifier, $languageSettings);
    }

    public function findProductTypes(
        ?ProductTypeQuery $query = null,
        ?LanguageSettings $languageSettings = null
    ): ProductTypeListInterface {
        return $this->innerService->findProductTypes($query, $languageSettings);
    }
}
