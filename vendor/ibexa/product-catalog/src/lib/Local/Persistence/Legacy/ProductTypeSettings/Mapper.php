<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Legacy\ProductTypeSettings;

use Ibexa\ProductCatalog\Local\Persistence\Values\ProductTypeSetting;

/**
 * @phpstan-import-type Data from \Ibexa\ProductCatalog\Local\Persistence\Legacy\ProductTypeSettings\GatewayInterface
 */
final class Mapper
{
    /**
     * @phpstan-param Data $row
     */
    public function createFromRow(array $row): ProductTypeSetting
    {
        return new ProductTypeSetting(
            $row['id'],
            $row['field_definition_id'],
            $row['is_virtual'],
        );
    }

    /**
     * @param array<Data> $rows
     *
     * @return array<\Ibexa\ProductCatalog\Local\Persistence\Values\ProductTypeSetting>
     */
    public function createFromRows(array $rows): array
    {
        $settings = [];
        foreach ($rows as $row) {
            $settings[] = $this->createFromRow($row);
        }

        return $settings;
    }
}
