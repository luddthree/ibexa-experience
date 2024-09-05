<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Data\Catalog;

final class CatalogBulkDeleteData
{
    /** @var \Ibexa\Contracts\ProductCatalog\Values\CatalogInterface[] */
    private array $catalogs;

    /**
     * @param \Ibexa\Contracts\ProductCatalog\Values\CatalogInterface[] $catalogs
     */
    public function __construct(array $catalogs = [])
    {
        $this->catalogs = $catalogs;
    }

    /**
     * @return \Ibexa\Contracts\ProductCatalog\Values\CatalogInterface[]
     */
    public function getCatalogs(): array
    {
        return $this->catalogs;
    }

    /**
     * @param \Ibexa\Contracts\ProductCatalog\Values\CatalogInterface[] $catalogs
     */
    public function setCatalogs(array $catalogs): void
    {
        $this->catalogs = $catalogs;
    }
}
