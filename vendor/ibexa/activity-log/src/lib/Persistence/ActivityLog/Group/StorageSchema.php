<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ActivityLog\Persistence\ActivityLog\Group;

final class StorageSchema
{
    public const TABLE_NAME = 'ibexa_activity_log_group';

    public const COLUMN_ID = 'id';
    public const COLUMN_SOURCE_ID = 'source_id';
    public const COLUMN_DESCRIPTION = 'description';
    public const COLUMN_LOGGED_AT = 'logged_at';
    public const COLUMN_USER_ID = 'user_id';
    public const COLUMN_IP_ID = 'ip_id';
}
