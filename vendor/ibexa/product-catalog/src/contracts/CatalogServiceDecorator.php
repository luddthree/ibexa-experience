<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog;

use Ibexa\Contracts\ProductCatalog\Values\Catalog\CatalogCopyStruct;
use Ibexa\Contracts\ProductCatalog\Values\Catalog\CatalogCreateStruct;
use Ibexa\Contracts\ProductCatalog\Values\Catalog\CatalogDeleteTranslationStruct;
use Ibexa\Contracts\ProductCatalog\Values\Catalog\CatalogListInterface;
use Ibexa\Contracts\ProductCatalog\Values\Catalog\CatalogQuery;
use Ibexa\Contracts\ProductCatalog\Values\Catalog\CatalogUpdateStruct;
use Ibexa\Contracts\ProductCatalog\Values\CatalogInterface;

abstract class CatalogServiceDecorator implements CatalogServiceInterface
{
    protected CatalogServiceInterface $innerService;

    public function __construct(CatalogServiceInterface $innerService)
    {
        $this->innerService = $innerService;
    }

    public function findCatalogs(?CatalogQuery $query = null): CatalogListInterface
    {
        return $this->innerService->findCatalogs($query);
    }

    /**
     * @param string[]|null $prioritizedLanguages
     */
    public function getCatalogByIdentifier(
        string $identifier,
        ?array $prioritizedLanguages = null
    ): CatalogInterface {
        return $this->innerService->getCatalogByIdentifier($identifier, $prioritizedLanguages);
    }

    public function createCatalog(CatalogCreateStruct $createStruct): CatalogInterface
    {
        return $this->innerService->createCatalog($createStruct);
    }

    public function updateCatalog(
        CatalogInterface $catalog,
        CatalogUpdateStruct $updateStruct
    ): CatalogInterface {
        return $this->innerService->updateCatalog($catalog, $updateStruct);
    }

    public function deleteCatalog(CatalogInterface $catalog): void
    {
        $this->innerService->deleteCatalog($catalog);
    }

    /**
     * @param string[]|null $prioritizedLanguages
     */
    public function getCatalog(int $id, ?array $prioritizedLanguages = null): CatalogInterface
    {
        return $this->innerService->getCatalog($id, $prioritizedLanguages);
    }

    public function copyCatalog(
        CatalogInterface $catalog,
        CatalogCopyStruct $copyStruct
    ): CatalogInterface {
        return $this->innerService->copyCatalog($catalog, $copyStruct);
    }

    public function deleteCatalogTranslation(CatalogDeleteTranslationStruct $struct): void
    {
        $this->innerService->deleteCatalogTranslation($struct);
    }
}
