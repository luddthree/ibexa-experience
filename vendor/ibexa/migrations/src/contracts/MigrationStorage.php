<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Migration;

use Ibexa\Migration\Repository\Migration;

interface MigrationStorage
{
    /**
     * Load a single migration from storage.
     */
    public function loadOne(string $name): ?Migration;

    /**
     * Load all migration present in storage.
     *
     * @return \Ibexa\Migration\Repository\Migration[]
     */
    public function loadAll(): array;

    /**
     * Store a migration in storage.
     *
     * @throws \Ibexa\Contracts\Migration\Exception\MigrationAlreadyExistsException if adding migration would cause an existing migration to become overwritten
     */
    public function store(Migration $migration): void;
}

class_alias(MigrationStorage::class, 'Ibexa\Platform\Contracts\Migration\MigrationStorage');
