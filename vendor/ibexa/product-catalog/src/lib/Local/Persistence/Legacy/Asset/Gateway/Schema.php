<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Legacy\Asset\Gateway;

final class Schema
{
    public const TABLE_NAME = 'ibexa_product_specification_asset';

    public const COLUMN_ID = 'id';
    public const COLUMN_PRODUCT_SPECIFICATION_ID = 'product_specification_id';
    public const COLUMN_URI = 'uri';
    public const COLUMN_TAGS = 'tags';
}
