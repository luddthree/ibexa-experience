<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Legacy\Catalog\Gateway;

final class StorageSchema
{
    public const TABLE_NAME = 'ibexa_catalog';

    public const COLUMN_ID = 'id';
    public const COLUMN_IDENTIFIER = 'identifier';
    public const COLUMN_NAME = 'name';
    public const COLUMN_NAME_NORMALIZED = 'name_normalized';
    public const COLUMN_DESCRIPTION = 'description';
    public const COLUMN_CREATOR_ID = 'creator_id';
    public const COLUMN_CREATED = 'created';
    public const COLUMN_MODIFIED = 'modified';
    public const COLUMN_STATUS = 'status';
    public const COLUMN_QUERY_STRING = 'query_string';
}
