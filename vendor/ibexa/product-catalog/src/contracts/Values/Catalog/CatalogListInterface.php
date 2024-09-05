<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Values\Catalog;

use Countable;
use IteratorAggregate;

/**
 * @extends IteratorAggregate<\Ibexa\Contracts\ProductCatalog\Values\CatalogInterface>
 */
interface CatalogListInterface extends IteratorAggregate, Countable
{
    /**
     * Partial list of catalogs.
     *
     * @return \Ibexa\Contracts\ProductCatalog\Values\CatalogInterface[]
     */
    public function getCatalogs(): array;

    /**
     * Return total count of found catalogs (filtered by permissions).
     */
    public function getTotalCount(): int;
}
