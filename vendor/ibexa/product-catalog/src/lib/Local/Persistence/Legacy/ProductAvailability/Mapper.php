<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Legacy\ProductAvailability;

use Ibexa\ProductCatalog\Local\Persistence\Values\ProductAvailability;

final class Mapper
{
    /**
     * @phpstan-param array{
     *   id: int,
     *   availability: bool,
     *   is_infinite: bool,
     *   stock: int|null,
     *   product_code: string
     * } $row
     */
    public function createFromRow(array $row): ProductAvailability
    {
        $stock = $row['stock'] === null ? $row['stock'] : (int) $row['stock'];

        return new ProductAvailability(
            $row['id'],
            $row['availability'],
            $row['is_infinite'],
            $stock,
            $row['product_code'],
        );
    }
}
