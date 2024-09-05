<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\Metadata\Storage;

use Ibexa\Contracts\Migration\Metadata\Storage\MetadataStorage;
use Ibexa\Migration\Metadata\ExecutedMigration;
use Ibexa\Migration\Metadata\ExecutedMigrationsList;
use Ibexa\Migration\Metadata\ExecutionResult;

final class InMemoryMetadataStorage implements MetadataStorage
{
    /** @var \Ibexa\Migration\Metadata\ExecutedMigration[] */
    private $migrations;

    /**
     * @param \Ibexa\Migration\Metadata\ExecutedMigration[] $migrations
     */
    public function __construct(array $migrations = [])
    {
        $this->migrations = $migrations;
    }

    public function ensureInitialized(): void
    {
    }

    public function getExecutedMigrations(): ExecutedMigrationsList
    {
        return new ExecutedMigrationsList($this->migrations);
    }

    public function complete(ExecutionResult $result): void
    {
        $this->migrations[] = new ExecutedMigration(
            $result->getName(),
            $result->getExecutedAt(),
            $result->getTime()
        );
    }

    public function reset(): void
    {
        $this->migrations = [];
    }
}

class_alias(InMemoryMetadataStorage::class, 'Ibexa\Platform\Migration\Metadata\Storage\InMemoryMetadataStorage');
