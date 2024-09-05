<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\Search\Solr\SortClause;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause;
use Ibexa\Contracts\ProductCatalog\Values\Content\Query\SortClause\ProductSortClauseAdapter;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\SortClause\ProductStock;
use Ibexa\Contracts\Solr\Query\SortClauseVisitor;
use Ibexa\ProductCatalog\Local\Repository\Search\Common\IndexDataProvider\ProductSpecificationIndexDataProvider;

final class ProductStockVisitor extends SortClauseVisitor
{
    private const FIELD_NAME_IS_INFINITE = ProductSpecificationIndexDataProvider::INDEX_IS_INFINITE_STOCK . '_b';
    private const FIELD_NAME_STOCK = ProductSpecificationIndexDataProvider::INDEX_PRODUCT_STOCK . '_i';

    public function canVisit(SortClause $sortClause): bool
    {
        if ($sortClause instanceof ProductSortClauseAdapter) {
            return $sortClause->getSortClause() instanceof ProductStock;
        }

        return false;
    }

    public function visit(SortClause $sortClause): string
    {
        return implode(', ', [
            self::FIELD_NAME_IS_INFINITE . $this->getDirection($sortClause),
            self::FIELD_NAME_STOCK . $this->getDirection($sortClause),
        ]);
    }
}
