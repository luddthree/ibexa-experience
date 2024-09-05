<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Values\Content\Query\SortClause;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\SortClause as ProductSortClause;

/**
 * Sort clause which acts as bridge between Product Catalog and Content Repository.
 *
 * @phpstan-template T of \Ibexa\Contracts\ProductCatalog\Values\Product\Query\SortClause
 */
final class ProductSortClauseAdapter extends SortClause
{
    /**
     * @phpstan-var T
     */
    private ProductSortClause $sortClause;

    /**
     * @phpstan-param T $sortClause
     */
    public function __construct(ProductSortClause $sortClause)
    {
        parent::__construct('product_sort_clause', $sortClause->getDirection());

        $this->sortClause = $sortClause;
    }

    /**
     * @phpstan-return T
     */
    public function getSortClause(): ProductSortClause
    {
        return $this->sortClause;
    }
}
