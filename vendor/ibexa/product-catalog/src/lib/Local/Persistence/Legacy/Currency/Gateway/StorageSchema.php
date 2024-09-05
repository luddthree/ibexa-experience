<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Legacy\Currency\Gateway;

final class StorageSchema
{
    public const TABLE_NAME = 'ibexa_currency';

    public const COLUMN_ID = 'id';
    public const COLUMN_CODE = 'code';
    public const COLUMN_SUBUNITS = 'subunits';
    public const COLUMN_ENABLED = 'enabled';
}
