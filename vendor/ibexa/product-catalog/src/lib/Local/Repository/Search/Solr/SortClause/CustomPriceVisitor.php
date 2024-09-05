<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\Search\Solr\SortClause;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause;
use Ibexa\Contracts\ProductCatalog\Values\Content\Query\SortClause\ProductSortClauseAdapter;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\SortClause\CustomPrice;
use Ibexa\Contracts\Solr\Query\SortClauseVisitor;
use Ibexa\ProductCatalog\Local\Repository\Search\Common\FieldNameBuilder\PriceFieldNameBuilder;

final class CustomPriceVisitor extends SortClauseVisitor
{
    public function canVisit(SortClause $sortClause): bool
    {
        if ($sortClause instanceof ProductSortClauseAdapter) {
            return $sortClause->getSortClause() instanceof CustomPrice;
        }

        return false;
    }

    /**
     * @phpstan-param \Ibexa\Contracts\ProductCatalog\Values\Content\Query\SortClause\ProductSortClauseAdapter<
     *     \Ibexa\Contracts\ProductCatalog\Values\Product\Query\SortClause\CustomPrice
     * > $sortClause
     *
     * @param \Ibexa\Contracts\ProductCatalog\Values\Content\Query\SortClause\ProductSortClauseAdapter $sortClause
     */
    public function visit(SortClause $sortClause): string
    {
        return $this->getTargetField($sortClause->getSortClause()) . $this->getDirection($sortClause);
    }

    private function getTargetField(CustomPrice $sortClause): string
    {
        $builder = new PriceFieldNameBuilder($sortClause->getCurrency()->getCode());
        if ($sortClause->getCustomerGroup() !== null) {
            $builder->withCustomerGroup($sortClause->getCustomerGroup()->getIdentifier());
        }

        return $builder->build() . '_i';
    }
}
