<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Legacy\Product\Gateway;

final class StorageSchema
{
    public const TABLE_NAME = 'ibexa_product';

    public const COLUMN_ID = 'id';
    public const COLUMN_CODE = 'code';
    public const COLUMN_IS_PUBLISHED = 'is_published';
    public const COLUMN_BASE_PRODUCT_ID = 'base_product_id';
}
