<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\Search\Solr\SortClause;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause;
use Ibexa\Contracts\ProductCatalog\Values\Content\Query\SortClause\ProductSortClauseAdapter;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\SortClause\ProductAvailability;
use Ibexa\Contracts\Solr\Query\SortClauseVisitor;
use Ibexa\ProductCatalog\Local\Repository\Search\Common\IndexDataProvider\ProductSpecificationIndexDataProvider;

final class ProductAvailabilityVisitor extends SortClauseVisitor
{
    public function canVisit(SortClause $sortClause): bool
    {
        if ($sortClause instanceof ProductSortClauseAdapter) {
            return $sortClause->getSortClause() instanceof ProductAvailability;
        }

        return false;
    }

    public function visit(SortClause $sortClause): string
    {
        return ProductSpecificationIndexDataProvider::INDEX_PRODUCT_AVAILABILITY . '_b' . $this->getDirection($sortClause);
    }
}
