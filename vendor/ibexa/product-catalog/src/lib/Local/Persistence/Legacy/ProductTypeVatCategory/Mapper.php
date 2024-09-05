<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Legacy\ProductTypeVatCategory;

use Ibexa\ProductCatalog\Local\Persistence\Values\ProductTypeVatCategory;

final class Mapper
{
    /**
     * @phpstan-param array{
     *     id: int,
     *     field_definition_id: int,
     *     region: string,
     *     vat_category: string,
     * } $row
     */
    public function createFromRow(array $row): ProductTypeVatCategory
    {
        return new ProductTypeVatCategory(
            $row['id'],
            $row['field_definition_id'],
            $row['region'],
            $row['vat_category'],
        );
    }

    /**
     * @phpstan-param array<array{
     *     id: int,
     *     field_definition_id: int,
     *     region: string,
     *     vat_category: string,
     * }> $rows
     *
     * @return array<\Ibexa\ProductCatalog\Local\Persistence\Values\ProductTypeVatCategory>
     */
    public function createFromRows(array $rows): array
    {
        return array_map(
            [$this, 'createFromRow'],
            $rows,
        );
    }
}
