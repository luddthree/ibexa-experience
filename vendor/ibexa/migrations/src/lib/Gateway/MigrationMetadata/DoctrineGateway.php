<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\Gateway\MigrationMetadata;

use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Connections\PrimaryReadReplicaConnection;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Doctrine\DBAL\Schema\Comparator;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Schema\TableDiff;
use Doctrine\DBAL\Types\Types;
use Ibexa\Contracts\Migration\Exception\MetadataStorageError;
use Ibexa\Migration\Metadata\ExecutedMigration;
use Ibexa\Migration\Metadata\ExecutionResult;

/**
 * @internal
 */
final class DoctrineGateway
{
    /** @var \Doctrine\DBAL\Connection */
    private $connection;

    /** @var \Doctrine\DBAL\Schema\AbstractSchemaManager */
    private $schemaManager;

    /** @var \Ibexa\Migration\Gateway\MigrationMetadata\SchemaProvider */
    private $schemaProvider;

    public function __construct(Connection $connection, SchemaProvider $schemaProvider)
    {
        $this->connection = $connection;
        $this->schemaProvider = $schemaProvider;
    }

    /**
     * Fetches instances of \Ibexa\Migration\Metadata\ExecutedMigration, indexed by their name.
     *
     * @return iterable<string, \Ibexa\Migration\Metadata\ExecutedMigration>
     */
    public function fetchExecutedMigrations(): iterable
    {
        $qb = $this->connection->createQueryBuilder();
        $qb->from($this->connection->quoteIdentifier($this->schemaProvider->getTableName()), 'im');
        $qb->select(
            sprintf(
                '%s as %s',
                $this->connection->quoteIdentifier(sprintf(
                    'im.%s',
                    $this->schemaProvider->getNameColumn(),
                )),
                'name',
            ),
            sprintf(
                '%s as %s',
                $this->connection->quoteIdentifier(sprintf(
                    'im.%s',
                    $this->schemaProvider->getExecutedAtColumn(),
                )),
                'executed_at',
            ),
            sprintf(
                '%s as %s',
                $this->connection->quoteIdentifier(sprintf(
                    'im.%s',
                    $this->schemaProvider->getExecutionTimeColumn(),
                )),
                'execution_time',
            ),
        );

        foreach ($qb->execute() as $row) {
            $name = $row['name'];
            $executedAt = $row['executed_at'] ?? '';

            $platform = $this->connection->getDatabasePlatform();
            $executedAt = $executedAt !== ''
                ? DateTimeImmutable::createFromFormat($platform->getDateTimeFormatString(), $executedAt)
                : null;

            $executionTime = isset($row['execution_time'])
                ? (float) ($row['execution_time'])
                : null;

            $migration = new ExecutedMigration(
                $name,
                $executedAt instanceof DateTimeImmutable ? $executedAt : null,
                $executionTime,
            );

            yield (string) $name => $migration;
        }
    }

    public function complete(ExecutionResult $result): void
    {
        $executionTime = $result->getTime() === null ? null : (int) round($result->getTime());

        $qb = $this->connection->createQueryBuilder();
        $qb->insert($this->schemaProvider->getTableName());
        $qb->values([
            $this->schemaProvider->getNameColumn() => $qb->createNamedParameter(
                $result->getName(),
                Types::STRING,
                ':name',
            ),
            $this->schemaProvider->getExecutedAtColumn() => $qb->createNamedParameter(
                $result->getExecutedAt(),
                Types::DATETIME_MUTABLE,
                ':executed_at',
            ),
            $this->schemaProvider->getExecutionTimeColumn() => $qb->createNamedParameter(
                $executionTime,
                Types::INTEGER,
                ':execution_time',
            ),
        ]);

        $qb->execute();
    }

    public function reset(): void
    {
        $qb = $this->connection->createQueryBuilder();
        $qb->delete($this->connection->quoteIdentifier($this->schemaProvider->getTableName()));
        $qb->execute();
    }

    public function isInitialized(): bool
    {
        if ($this->connection instanceof PrimaryReadReplicaConnection) {
            $this->connection->connect('master');
        }

        return $this->getSchemaManager()->tablesExist([$this->schemaProvider->getTableName()]);
    }

    public function checkInitialization(): void
    {
        if (!$this->isInitialized()) {
            throw MetadataStorageError::notInitialized();
        }

        $expectedTable = $this->buildExpectedTable();

        if ($this->needsUpdate($expectedTable) !== null) {
            throw MetadataStorageError::notUpToDate();
        }
    }

    /**
     * Makes sure that migration metadata table contains proper, up-to-date schema.
     */
    public function ensureInitialized(): void
    {
        if (!$this->isInitialized()) {
            $expectedSchemaChangelog = $this->buildExpectedTable();
            $this->getSchemaManager()->createTable($expectedSchemaChangelog);

            return;
        }

        $expectedSchemaChangelog = $this->buildExpectedTable();
        $diff = $this->needsUpdate($expectedSchemaChangelog);
        if ($diff === null) {
            return;
        }

        $this->getSchemaManager()->alterTable($diff);
    }

    private function needsUpdate(Table $expectedTable): ?TableDiff
    {
        $comparator = new Comparator();
        $currentTable = $this->connection->getSchemaManager()->listTableDetails(
            $this->schemaProvider->getTableName(),
        );
        $diff = $comparator->diffTable($currentTable, $expectedTable);

        return $diff instanceof TableDiff ? $diff : null;
    }

    /**
     * Creates or updates a Table instance.
     */
    private function buildExpectedTable(): Table
    {
        return $this->schemaProvider->buildExpectedTable();
    }

    /**
     * Resolves ScheManager on first use and returns the instance.
     *
     * This prevents from premature siteaccess initialization
     * by accessing Connection before it's been resolved in
     * proper context.
     */
    private function getSchemaManager(): AbstractSchemaManager
    {
        if (null === $this->schemaManager) {
            $this->schemaManager = $this->connection->getSchemaManager();
        }

        return $this->schemaManager;
    }
}

class_alias(DoctrineGateway::class, 'Ibexa\Platform\Migration\Gateway\MigrationMetadata\DoctrineGateway');
