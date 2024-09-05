<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Value;

use Ibexa\Rest\Value;

final class CatalogList extends Value
{
    /** @var \Ibexa\Bundle\ProductCatalog\REST\Value\Catalog[] */
    public array $catalogs = [];

    /**
     * @param \Ibexa\Bundle\ProductCatalog\REST\Value\Catalog[] $catalogs
     */
    public function __construct(array $catalogs)
    {
        $this->catalogs = $catalogs;
    }
}
