<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\Search\Legacy\SortClause;

use Doctrine\DBAL\Query\QueryBuilder;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause;
use Ibexa\Contracts\ProductCatalog\Values\Content\Query\SortClause\ProductSortClauseAdapter;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\SortClause\ProductName;
use Ibexa\Core\Search\Legacy\Content\Common\Gateway\SortClauseHandler;
use Ibexa\ProductCatalog\FieldType\ProductSpecification\Persistence\Gateway\StorageSchema;

final class ProductNameVisitor extends SortClauseHandler
{
    public function accept(SortClause $sortClause): bool
    {
        if ($sortClause instanceof ProductSortClauseAdapter) {
            return $sortClause->getSortClause() instanceof ProductName;
        }

        return false;
    }

    /**
     * @phpstan-param ProductSortClauseAdapter<ProductName> $sortClause
     *
     * @return string[]
     */
    public function applySelect(QueryBuilder $query, SortClause $sortClause, int $number): array
    {
        $column = $this->getSortColumnName($number);

        $query->addSelect(sprintf('c.name AS %s', $column));

        return [$column];
    }

    /**
     * @phpstan-param ProductSortClauseAdapter<ProductName> $sortClause
     * @phpstan-param array<string,mixed> $languageSettings
     */
    public function applyJoin(
        QueryBuilder $query,
        SortClause $sortClause,
        int $number,
        array $languageSettings
    ): void {
        $table = $this->getSortTableName($number);

        $query->leftJoin(
            'c',
            StorageSchema::TABLE_NAME,
            $table,
            sprintf('c.id = %s.content_id AND c.current_version = %s.version_no', $table, $table)
        );
    }
}
