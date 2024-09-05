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
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\SortClause\ProductStock;
use Ibexa\Core\Search\Legacy\Content\Common\Gateway\SortClauseHandler;
use Ibexa\ProductCatalog\FieldType\ProductSpecification\Persistence\Gateway\StorageSchema as SpecificationStorageSchema;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\ProductAvailability\Gateway\StorageSchema as AvailabilityStorageSchema;

final class ProductStockVisitor extends SortClauseHandler
{
    public function accept(SortClause $sortClause): bool
    {
        if ($sortClause instanceof ProductSortClauseAdapter) {
            return $sortClause->getSortClause() instanceof ProductStock;
        }

        return false;
    }

    /**
     * @phpstan-param ProductSortClauseAdapter<ProductStock> $sortClause
     *
     * @return string[]
     */
    public function applySelect(QueryBuilder $query, SortClause $sortClause, int $number): array
    {
        $table = $this->getSortTableName($number);
        $alias = $this->getSortColumnName($number);

        $select = sprintf(
            '%s.stock AS %s, %s.is_infinite AS is_infinite',
            $table,
            $alias,
            $table,
        );

        $query->addSelect($select);

        return ['is_infinite', $alias];
    }

    /**
     * @phpstan-param ProductSortClauseAdapter<ProductStock> $sortClause
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
            SpecificationStorageSchema::TABLE_NAME,
            'product_specification',
            sprintf('c.id = %1$s.content_id AND c.current_version = %1$s.version_no', 'product_specification')
        );
        $query->leftJoin(
            'product_specification',
            AvailabilityStorageSchema::TABLE_NAME,
            $table,
            sprintf('product_specification.code = %s.product_code', $table)
        );
    }
}
