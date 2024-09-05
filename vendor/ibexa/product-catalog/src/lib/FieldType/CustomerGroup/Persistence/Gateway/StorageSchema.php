<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\FieldType\CustomerGroup\Persistence\Gateway;

final class StorageSchema
{
    public const TABLE_NAME = 'ibexa_content_customer_group';

    public const COLUMN_ID = 'id';
    public const COLUMN_FIELD_ID = 'field_id';
    public const COLUMN_FIELD_VERSION_NO = 'field_version_no';
    public const COLUMN_CONTENT_ID = 'content_id';
    public const COLUMN_CUSTOMER_GROUP_ID = 'customer_group_id';
}
