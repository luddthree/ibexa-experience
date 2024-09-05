<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\Storage;

use Ibexa\Contracts\Migration\MigrationStorage;
use Ibexa\Migration\Repository\Migration;

final class InMemoryMigrationStorage implements MigrationStorage
{
    /** @var \Ibexa\Migration\Repository\Migration[] */
    private $migrations;

    /**
     * @param \Ibexa\Migration\Repository\Migration[] $migrations
     */
    public function __construct(array $migrations = [])
    {
        $this->migrations = $migrations;
    }

    public function loadOne(string $name): ?Migration
    {
        return $this->migrations[$name] ?? null;
    }

    public function loadAll(): array
    {
        return $this->migrations;
    }

    public function store(Migration $migration): void
    {
        $this->migrations[$migration->getName()] = $migration;
    }
}

class_alias(InMemoryMigrationStorage::class, 'Ibexa\Platform\Migration\Storage\InMemoryMigrationStorage');
