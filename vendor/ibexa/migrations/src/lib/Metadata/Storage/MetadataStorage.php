<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\Metadata\Storage;

use Ibexa\Contracts\Migration\Metadata\Storage\MetadataStorage as MetadataStorageInterface;
use Ibexa\Migration\Gateway\MigrationMetadata\DoctrineGateway;
use Ibexa\Migration\Metadata\ExecutedMigration;
use Ibexa\Migration\Metadata\ExecutedMigrationsList;
use Ibexa\Migration\Metadata\ExecutionResult;

final class MetadataStorage implements MetadataStorageInterface
{
    /** @var \Ibexa\Migration\Gateway\MigrationMetadata\DoctrineGateway */
    private $gateway;

    public function __construct(DoctrineGateway $gateway)
    {
        $this->gateway = $gateway;
    }

    public function ensureInitialized(): void
    {
        $this->gateway->ensureInitialized();
    }

    public function getExecutedMigrations(): ExecutedMigrationsList
    {
        if (!$this->isInitialized()) {
            return new ExecutedMigrationsList([]);
        }

        $this->checkInitialization();

        $migrations = [];
        foreach ($this->gateway->fetchExecutedMigrations() as $migrationName => $migration) {
            $migrations[$migrationName] = $migration;
        }

        uasort($migrations, static function (ExecutedMigration $a, ExecutedMigration $b): int {
            return $a <=> $b;
        });

        return new ExecutedMigrationsList($migrations);
    }

    public function complete(ExecutionResult $result): void
    {
        $this->checkInitialization();
        $this->gateway->complete($result);
    }

    public function reset(): void
    {
        $this->checkInitialization();
        $this->gateway->reset();
    }

    private function isInitialized(): bool
    {
        return $this->gateway->isInitialized();
    }

    private function checkInitialization(): void
    {
        $this->gateway->checkInitialization();
    }
}

class_alias(MetadataStorage::class, 'Ibexa\Platform\Migration\Metadata\Storage\MetadataStorage');
