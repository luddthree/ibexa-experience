<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Value;

use Ibexa\Rest\Value;

final class CatalogProductFilterList extends Value
{
    /** @var \Ibexa\Bundle\ProductCatalog\REST\Value\CatalogProductFilter[] */
    public array $filters = [];

    /**
     * @param \Ibexa\Bundle\ProductCatalog\REST\Value\CatalogProductFilter[] $filters
     */
    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }
}
