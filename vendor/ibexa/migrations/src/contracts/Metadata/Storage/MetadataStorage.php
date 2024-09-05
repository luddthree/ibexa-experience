<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Migration\Metadata\Storage;

use Ibexa\Migration\Metadata\ExecutedMigrationsList;
use Ibexa\Migration\Metadata\ExecutionResult;

/**
 * Responsible for handling migration metadata. In particular, what migrations are already executed and when.
 */
interface MetadataStorage
{
    /**
     * Prepares gateway for writing/reading. For relational databases this would usually mean creating or updating
     * a table and it's columns.
     */
    public function ensureInitialized(): void;

    /**
     * Queries gateway to get all executed migrations. This also includes migrations that were previously executed, but
     * are not longer stored in migration directory (i.e. are no longer available).
     */
    public function getExecutedMigrations(): ExecutedMigrationsList;

    /**
     * Mark migration as completed.
     */
    public function complete(ExecutionResult $result): void;

    /**
     * Resets gateway to it's original state. Removes all information about executed migrations.
     */
    public function reset(): void;
}

class_alias(MetadataStorage::class, 'Ibexa\Platform\Contracts\Migration\Metadata\Storage\MetadataStorage');
