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

interface CatalogServiceInterface
{
    public function findCatalogs(?CatalogQuery $query = null): CatalogListInterface;

    /**
     * @param string[]|null $prioritizedLanguages
     *
     * @throws \Ibexa\ProductCatalog\Exception\UnauthorizedException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    public function getCatalogByIdentifier(
        string $identifier,
        ?array $prioritizedLanguages = null
    ): CatalogInterface;

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function createCatalog(CatalogCreateStruct $createStruct): CatalogInterface;

    /**
     * @throws \Ibexa\ProductCatalog\Exception\UnauthorizedException
     * @throws \Ibexa\Contracts\Core\Exception\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    public function updateCatalog(
        CatalogInterface $catalog,
        CatalogUpdateStruct $updateStruct
    ): CatalogInterface;

    /**
     * @throws \Ibexa\ProductCatalog\Exception\UnauthorizedException
     * @throws \Ibexa\Core\Base\Exceptions\NotFoundException
     */
    public function deleteCatalog(CatalogInterface $catalog): void;

    /**
     * @param string[]|null $prioritizedLanguages
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\ProductCatalog\Exception\UnauthorizedException
     */
    public function getCatalog(int $id, ?array $prioritizedLanguages = null): CatalogInterface;

    public function copyCatalog(
        CatalogInterface $catalog,
        CatalogCopyStruct $copyStruct
    ): CatalogInterface;

    public function deleteCatalogTranslation(CatalogDeleteTranslationStruct $struct): void;
}
