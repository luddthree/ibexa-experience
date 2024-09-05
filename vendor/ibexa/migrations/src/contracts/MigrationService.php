<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Migration;

use Ibexa\Migration\Repository\Migration;

interface MigrationService
{
    /**
     * Adds a migration into their storage.
     *
     * @throws \Ibexa\Contracts\Migration\Exception\MigrationAlreadyExistsException if adding migration would cause an existing migration to become overwritten
     */
    public function add(Migration $migration): void;

    /**
     * Return Migrations ordered alphabetically by their names.
     *
     * @return \Ibexa\Migration\Repository\Migration[]
     */
    public function listMigrations(): array;

    /**
     * Checks if Migration is already marked as executed in database.
     */
    public function isMigrationExecuted(Migration $migration): bool;

    /**
     * Executes a single migration and stores the result in metadata storage.
     *
     * @param \Ibexa\Migration\Repository\Migration $migration
     * @param string|null $userLogin User login to execute migrations as. If not passed a default one should be used.
     *
     * @throws \Ibexa\Contracts\Migration\Exception\UnhandledMigrationException when Migration execution fails.
     * @throws \Ibexa\Contracts\Migration\Exception\InvalidMigrationException when Migration format is invalid.
     * @throws \Ibexa\Contracts\Migration\Exception\MigrationAlreadyExecutedException when migration is already present in metadata storage.
     */
    public function executeOne(Migration $migration, ?string $userLogin = null): void;

    /**
     * Executes all available (present in migration storage) migration that are not yet executed.
     *
     * @param string|null $userLogin User login to execute migrations as. I not passed a default one should be used.
     *
     * @throws \Ibexa\Contracts\Migration\Exception\UnhandledMigrationException when Migration execution fails.
     * @throws \Ibexa\Contracts\Migration\Exception\InvalidMigrationException when Migration format is invalid.
     * @throws \Ibexa\Contracts\Migration\Exception\MigrationAlreadyExecutedException when migration is already present in metadata storage.
     */
    public function executeAll(?string $userLogin = null): void;

    /**
     * Find a single Migration.
     *
     * @param string $name
     */
    public function findOneByName(string $name): ?Migration;
}

class_alias(MigrationService::class, 'Ibexa\Platform\Contracts\Migration\MigrationService');
