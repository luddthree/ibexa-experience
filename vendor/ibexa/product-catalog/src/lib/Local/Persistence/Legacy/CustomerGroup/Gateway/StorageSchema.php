<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Legacy\CustomerGroup\Gateway;

final class StorageSchema
{
    public const TABLE_NAME = 'ibexa_customer_group';

    public const COLUMN_ID = 'id';
    public const COLUMN_IDENTIFIER = 'identifier';
    public const COLUMN_NAME = 'name';
    public const COLUMN_NAME_NORMALIZED = 'name_normalized';
    public const COLUMN_DESCRIPTION = 'description';
    public const COLUMN_GLOBAL_PRICE_RATE = 'global_price_rate';
}
