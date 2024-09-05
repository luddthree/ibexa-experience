<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\Gateway\MigrationMetadata;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\Table;

/**
 * @internal
 */
final class SchemaProvider
{
    /** @deprecated MIGRATION_METADATA_TABLE has been deprecated since 3.3, and will be removed in 5.0. Use SchemaProvider::getTableName() instead */
    public const MIGRATION_METADATA_TABLE = 'ibexa_migrations';

    /** @deprecated MIGRATION_METADATA_NAME_COLUMN has been deprecated since 3.3, and will be removed in 5.0. Use SchemaProvider::getNameColumn() instead */
    public const MIGRATION_METADATA_NAME_COLUMN = 'name';
    /** @deprecated MIGRATION_METADATA_EXECUTED_AT_COLUMN has been deprecated since 3.3, and will be removed in 5.0. Use SchemaProvider::getExecutedAtColumn() instead */
    public const MIGRATION_METADATA_EXECUTED_AT_COLUMN = 'executed_at';
    /** @deprecated MIGRATION_METADATA_EXECUTION_TIME_COLUMN has been deprecated since 3.3, and will be removed in 5.0. Use SchemaProvider::getExecutionTimeColumn() instead */
    public const MIGRATION_METADATA_EXECUTION_TIME_COLUMN = 'execution_time';

    /** @deprecated MIGRATION_METADATA_NAME_COLUMN_LENGTH has been deprecated since 3.3, and will be removed in 5.0. No replacement has been provided. */
    public const MIGRATION_METADATA_NAME_COLUMN_LENGTH = 191;

    /** @var string */
    private $tableName;

    /** @var string */
    private $nameColumn;

    /** @var string */
    private $executedAtColumn;

    /** @var string */
    private $executionTimeColumn;

    public function __construct(
        string $tableName = self::MIGRATION_METADATA_TABLE,
        string $nameColumn = self::MIGRATION_METADATA_NAME_COLUMN,
        string $executedAtColumn = self::MIGRATION_METADATA_EXECUTED_AT_COLUMN,
        string $executionTimeColumn = self::MIGRATION_METADATA_EXECUTION_TIME_COLUMN
    ) {
        $this->tableName = $tableName;
        $this->nameColumn = $nameColumn;
        $this->executedAtColumn = $executedAtColumn;
        $this->executionTimeColumn = $executionTimeColumn;
    }

    public function getTableName(): string
    {
        return $this->tableName;
    }

    public function getNameColumn(): string
    {
        return $this->nameColumn;
    }

    public function getExecutedAtColumn(): string
    {
        return $this->executedAtColumn;
    }

    public function getExecutionTimeColumn(): string
    {
        return $this->executionTimeColumn;
    }

    /**
     * Creates or updates a Table instance.
     */
    public function buildExpectedTable(?Schema $schema = null): Table
    {
        if ($schema !== null) {
            $table = $schema->createTable($this->tableName);
        } else {
            $table = new Table($this->tableName);
        }

        $table->addColumn(
            $this->nameColumn,
            'string',
            ['notnull' => true, 'length' => self::MIGRATION_METADATA_NAME_COLUMN_LENGTH],
        );
        $table->addColumn(
            $this->executedAtColumn,
            'datetime',
            ['notnull' => false],
        );
        $table->addColumn(
            $this->executionTimeColumn,
            'integer',
            ['notnull' => false],
        );

        $table->setPrimaryKey([$this->nameColumn]);

        return $table;
    }
}

class_alias(SchemaProvider::class, 'Ibexa\Platform\Migration\Gateway\MigrationMetadata\SchemaProvider');
