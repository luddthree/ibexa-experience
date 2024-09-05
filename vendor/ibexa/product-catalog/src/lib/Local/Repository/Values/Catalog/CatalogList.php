<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\Values\Catalog;

use ArrayIterator;
use Ibexa\Contracts\ProductCatalog\Values\Catalog\CatalogListInterface;
use Traversable;

final class CatalogList implements CatalogListInterface
{
    /** @var \Ibexa\Contracts\ProductCatalog\Values\CatalogInterface[] */
    private array $catalogs;

    private int $totalCount;

    /**
     * @param \Ibexa\Contracts\ProductCatalog\Values\CatalogInterface[] $catalogs
     */
    public function __construct(array $catalogs = [], int $total = 0)
    {
        $this->catalogs = $catalogs;
        $this->totalCount = $total;
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->catalogs);
    }

    public function count(): int
    {
        return count($this->catalogs);
    }

    public function getCatalogs(): array
    {
        return $this->catalogs;
    }

    public function getTotalCount(): int
    {
        return $this->totalCount;
    }
}
