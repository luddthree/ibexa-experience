<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\Search\ElasticSearch\SortClause;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause;
use Ibexa\Contracts\Elasticsearch\Query\LanguageFilter;
use Ibexa\Contracts\Elasticsearch\Query\SortClauseVisitor;
use Ibexa\Contracts\ProductCatalog\Values\Content\Query\SortClause\ProductSortClauseAdapter;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\SortClause\BasePrice;
use Ibexa\Elasticsearch\Query\SortClauseVisitor\AbstractSortClauseVisitor;
use Ibexa\ProductCatalog\Local\Repository\Search\Common\FieldNameBuilder\PriceFieldNameBuilder;

final class BasePriceVisitor extends AbstractSortClauseVisitor
{
    public function supports(SortClause $sortClause, LanguageFilter $languageFilter): bool
    {
        if ($sortClause instanceof ProductSortClauseAdapter) {
            return $sortClause->getSortClause() instanceof BasePrice;
        }

        return false;
    }

    /**
     * @phpstan-param \Ibexa\Contracts\ProductCatalog\Values\Content\Query\SortClause\ProductSortClauseAdapter<
     *     \Ibexa\Contracts\ProductCatalog\Values\Product\Query\SortClause\BasePrice
     * > $sortClause
     *
     * @param \Ibexa\Contracts\ProductCatalog\Values\Content\Query\SortClause\ProductSortClauseAdapter $sortClause
     *
     * @return array<string,string>
     */
    public function visit(
        SortClauseVisitor $visitor,
        SortClause $sortClause,
        LanguageFilter $languageFilter
    ): array {
        return [
            $this->getTargetField($sortClause->getSortClause()) => $this->getDirection($sortClause),
        ];
    }

    private function getTargetField(BasePrice $sortClause): string
    {
        $builder = new PriceFieldNameBuilder($sortClause->getCurrency()->getCode());

        return $builder->build() . '_i';
    }
}
