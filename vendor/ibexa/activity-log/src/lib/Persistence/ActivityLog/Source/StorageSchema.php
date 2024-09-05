<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ActivityLog\Persistence\ActivityLog\Source;

final class StorageSchema
{
    public const TABLE_NAME = 'ibexa_activity_log_group_source';

    public const COLUMN_ID = 'id';
    public const COLUMN_NAME = 'name';
}
