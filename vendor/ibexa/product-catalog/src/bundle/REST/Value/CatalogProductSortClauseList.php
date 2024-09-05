<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Value;

use Ibexa\Rest\Value;

final class CatalogProductSortClauseList extends Value
{
    /** @var \Ibexa\Bundle\ProductCatalog\REST\Value\CatalogProductSortClause[] */
    public array $sortClauses = [];

    /**
     * @param \Ibexa\Bundle\ProductCatalog\REST\Value\CatalogProductSortClause[] $sortClauses
     */
    public function __construct(array $sortClauses)
    {
        $this->sortClauses = $sortClauses;
    }
}
