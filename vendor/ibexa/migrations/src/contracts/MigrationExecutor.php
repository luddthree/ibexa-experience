<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Migration;

use Ibexa\Migration\Repository\Migration;

interface MigrationExecutor
{
    /**
     * Executes a single Migration.
     *
     * Migration should be wrapped in a transaction.
     *
     * @throws \Ibexa\Contracts\Migration\Exception\UnhandledMigrationException when Migration execution fails.
     * @throws \Ibexa\Contracts\Migration\Exception\InvalidMigrationException when Migration format is invalid.
     */
    public function execute(Migration $migration): void;
}

class_alias(MigrationExecutor::class, 'Ibexa\Platform\Contracts\Migration\MigrationExecutor');
