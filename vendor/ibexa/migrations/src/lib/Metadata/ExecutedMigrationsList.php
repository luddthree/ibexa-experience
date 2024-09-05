<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\Metadata;

use Countable;
use Ibexa\Contracts\Migration\Exception\MigrationNotExecuted;

/**
 * Represents a sorted list of executed migrations.
 * The migrations in this set might be not available anymore.
 */
final class ExecutedMigrationsList implements Countable
{
    /** @var ExecutedMigration[] */
    private $items;

    /**
     * @param ExecutedMigration[] $items
     */
    public function __construct(array $items)
    {
        $this->items = array_values($items);
    }

    /**
     * @return ExecutedMigration[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    public function count(): int
    {
        return count($this->items);
    }

    public function hasMigration(string $name): bool
    {
        foreach ($this->items as $migration) {
            if ($migration->getName() === $name) {
                return true;
            }
        }

        return false;
    }

    public function getMigration(string $name): ExecutedMigration
    {
        foreach ($this->items as $migration) {
            if ($migration->getName() === $name) {
                return $migration;
            }
        }

        throw new MigrationNotExecuted($name);
    }
}

class_alias(ExecutedMigrationsList::class, 'Ibexa\Platform\Migration\Metadata\ExecutedMigrationsList');
