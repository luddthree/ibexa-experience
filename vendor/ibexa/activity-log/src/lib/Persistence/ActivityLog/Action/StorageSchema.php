<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ActivityLog\Persistence\ActivityLog\Action;

final class StorageSchema
{
    public const TABLE_NAME = 'ibexa_activity_log_action';

    public const COLUMN_ID = 'id';
    public const COLUMN_ACTION = 'action';
    public const COLUMN_OBJECT_CLASS_ID = 'object_class_id';
}
