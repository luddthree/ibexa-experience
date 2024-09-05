<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\Search\Solr\SortClause;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause;
use Ibexa\Contracts\ProductCatalog\Values\Content\Query\SortClause\ProductSortClauseAdapter;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\SortClause\ProductCode;
use Ibexa\Contracts\Solr\Query\SortClauseVisitor;

final class ProductCodeVisitor extends SortClauseVisitor
{
    public function canVisit(SortClause $sortClause): bool
    {
        if ($sortClause instanceof ProductSortClauseAdapter) {
            return $sortClause->getSortClause() instanceof ProductCode;
        }

        return false;
    }

    public function visit(SortClause $sortClause): string
    {
        return 'product_code_id' . $this->getDirection($sortClause);
    }
}
