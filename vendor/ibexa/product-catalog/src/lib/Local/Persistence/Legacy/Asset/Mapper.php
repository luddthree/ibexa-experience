<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Legacy\Asset;

use Ibexa\ProductCatalog\Local\Persistence\Values\Asset;

/**
 * @phpstan-type Data array{
 *     id: int,
 *     product_specification_id: int,
 *     uri: non-empty-string,
 *     tags: string[]|null
 * }
 */
final class Mapper
{
    /**
     * @phpstan-param Data[] $rows
     *
     * @return \Ibexa\ProductCatalog\Local\Persistence\Values\Asset[]
     */
    public function extractFromRows(array $rows): array
    {
        $values = [];
        foreach ($rows as $row) {
            $values[] = $this->extractFromRow($row);
        }

        return $values;
    }

    /**
     * @phpstan-param Data $row
     */
    public function extractFromRow(array $row): Asset
    {
        $value = new Asset();
        $value->id = $row['id'];
        $value->productSpecificationId = $row['product_specification_id'];
        $value->uri = $row['uri'];
        $value->tags = $row['tags'] ?? [];

        return $value;
    }
}
