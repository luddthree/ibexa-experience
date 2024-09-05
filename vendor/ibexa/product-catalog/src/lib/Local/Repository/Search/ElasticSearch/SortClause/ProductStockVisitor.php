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
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\SortClause\ProductStock;
use Ibexa\Elasticsearch\Query\SortClauseVisitor\AbstractSortClauseVisitor;
use Ibexa\ProductCatalog\Local\Repository\Search\Common\IndexDataProvider\ProductSpecificationIndexDataProvider;

final class ProductStockVisitor extends AbstractSortClauseVisitor
{
    private const FIELD_NAME_IS_INFINITE = ProductSpecificationIndexDataProvider::INDEX_IS_INFINITE_STOCK . '_b';
    private const FIELD_NAME_STOCK = ProductSpecificationIndexDataProvider::INDEX_PRODUCT_STOCK . '_i';

    public function supports(SortClause $sortClause, LanguageFilter $languageFilter): bool
    {
        if ($sortClause instanceof ProductSortClauseAdapter) {
            return $sortClause->getSortClause() instanceof ProductStock;
        }

        return false;
    }

    /**
     * @return array<string, string>
     */
    public function visit(SortClauseVisitor $visitor, SortClause $sortClause, LanguageFilter $languageFilter): array
    {
        return [
            self::FIELD_NAME_IS_INFINITE => $this->getDirection($sortClause),
            self::FIELD_NAME_STOCK => $this->getDirection($sortClause),
        ];
    }
}
