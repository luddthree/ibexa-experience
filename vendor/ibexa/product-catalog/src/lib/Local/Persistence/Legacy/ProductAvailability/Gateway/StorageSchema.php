<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Legacy\ProductAvailability\Gateway;

final class StorageSchema
{
    public const TABLE_NAME = 'ibexa_product_specification_availability';

    public const COLUMN_ID = 'id';
    public const COLUMN_PRODUCT_CODE = 'product_code';
    public const COLUMN_AVAILABILITY = 'availability';
    public const COLUMN_STOCK = 'stock';
    public const COLUMN_IS_INFINITE = 'is_infinite';
}
