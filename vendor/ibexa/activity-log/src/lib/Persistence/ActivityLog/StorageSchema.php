<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ActivityLog\Persistence\ActivityLog;

final class StorageSchema
{
    public const TABLE_NAME = 'ibexa_activity_log';

    public const COLUMN_ID = 'id';
    public const COLUMN_ACTION_ID = 'action_id';
    public const COLUMN_OBJECT_CLASS_ID = 'object_class_id';
    public const COLUMN_OBJECT_ID = 'object_id';
    public const COLUMN_OBJECT_NAME = 'object_name';
    public const COLUMN_DATA = 'data';
    public const COLUMN_GROUP_ID = 'group_id';
}
