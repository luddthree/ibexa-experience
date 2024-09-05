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
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\SortClause\CreatedAt;
use Ibexa\Elasticsearch\Query\SortClauseVisitor\AbstractSortClauseVisitor;

final class CreatedAtVisitor extends AbstractSortClauseVisitor
{
    public function supports(SortClause $sortClause, LanguageFilter $languageFilter): bool
    {
        if ($sortClause instanceof ProductSortClauseAdapter) {
            return $sortClause->getSortClause() instanceof CreatedAt;
        }

        return false;
    }

    /**
     * @return array<string,string>
     */
    public function visit(
        SortClauseVisitor $visitor,
        SortClause $sortClause,
        LanguageFilter $languageFilter
    ): array {
        return [
            'content_publication_date_dt' => $this->getDirection($sortClause),
        ];
    }
}
