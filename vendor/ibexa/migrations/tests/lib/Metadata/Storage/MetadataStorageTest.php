<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Migration\Metadata\Storage;

use function array_keys;
use DAMA\DoctrineTestBundle\Doctrine\DBAL\StaticDriver;
use Doctrine\DBAL\Schema\Table;
use Ibexa\Contracts\Migration\Exception\MetadataStorageError;
use Ibexa\Migration\Metadata\ExecutionResult;
use Ibexa\Migration\Metadata\Storage\MetadataStorage;
use Ibexa\Tests\Bundle\Migration\IbexaKernelTestCase;

/**
 * @covers \Ibexa\Migration\Metadata\Storage\MetadataStorage
 */
final class MetadataStorageTest extends IbexaKernelTestCase
{
    private const TEST_TABLE_NAME = 'test_ibexa_migrations';
    private const TEST_COLUMN_NAME = 'test_name';
    private const TEST_COLUMN_EXECUTED_AT = 'test_executed_at';
    private const TEST_COLUMN_EXECUTION_TIME = 'test_execution_time';

    /** @var \Ibexa\Migration\Metadata\Storage\MetadataStorage */
    private $storage;

    /** @var \Doctrine\DBAL\Schema\AbstractSchemaManager */
    private $schemaManager;

    protected function setUp(): void
    {
        // Test executes DDL commands, which causes automatic transaction commit on MySQL + PHP8
        StaticDriver::setKeepStaticConnections(false);

        self::bootKernel();
        $this->storage = self::getServiceByClassName(MetadataStorage::class, sprintf(
            'migrations.%s',
            MetadataStorage::class,
        ));
        $this->schemaManager = self::getDoctrineConnection()->getSchemaManager();
    }

    protected function tearDown(): void
    {
        if ($this->schemaManager->tablesExist(self::TEST_TABLE_NAME)) {
            $this->schemaManager->dropTable(self::TEST_TABLE_NAME);
        }

        StaticDriver::setKeepStaticConnections(true);
    }

    /**
     * @dataProvider provideMethodCalls
     *
     * @param array<mixed>|null $arguments
     */
    public function testThrowsExceptionWhenMetadataStorageIsMissing(
        string $method,
        ?array $arguments = []
    ): void {
        // Sanity check
        self::assertFalse($this->schemaManager->tablesExist(self::TEST_TABLE_NAME));

        self::expectException(MetadataStorageError::class);
        self::expectExceptionMessage('Migrations metadata storage is not initialized. Make sure that Doctrine\'s '
            . 'database platform version is specified correctly, try running "ibexa:migrations:migrate command" or '
            . 'calling Ibexa\\Contracts\\Migration\\Metadata\\Storage\\MetadataStorage::ensureInitialized.');
        $this->storage->$method(...$arguments);
    }

    /**
     * @dataProvider provideMethodCalls
     *
     * @param array<mixed>|null $arguments
     */
    public function testThrowsExceptionWhenMetadataStorageIsPartiallyInitialized(
        string $method,
        ?array $arguments = []
    ): void {
        // Sanity check
        self::assertFalse($this->schemaManager->tablesExist(self::TEST_TABLE_NAME));

        $this->createIncompleteMigrationTable();

        self::expectException(MetadataStorageError::class);
        self::expectExceptionMessage('Migrations metadata storage is not up to date. Make sure that Doctrine\'s '
            . 'database platform version is specified correctly, try running "ibexa:migrations:migrate command" or '
            . 'calling Ibexa\\Contracts\\Migration\\Metadata\\Storage\\MetadataStorage::ensureInitialized.');
        $this->storage->$method(...$arguments);
    }

    /**
     * @phpstan-return iterable<array{
     *     string,
     *     1?: array<\Ibexa\Migration\Metadata\ExecutionResult>,
     * }>
     */
    public static function provideMethodCalls(): iterable
    {
        yield [
            'complete',
            [
                new ExecutionResult('foo'),
            ],
        ];

        yield [
            'reset',
        ];
    }

    public function testReturnsEmptyMigrationListWhenNotInitialized(): void
    {
        self::assertEmpty($this->storage->getExecutedMigrations());
        self::assertSame([], $this->storage->getExecutedMigrations()->getItems());
    }

    public function testEnsureInitializedInsertNewTableWhenMissing(): void
    {
        // Sanity check
        self::assertFalse($this->schemaManager->tablesExist(self::TEST_TABLE_NAME));

        $this->storage->ensureInitialized();

        self::assertTrue($this->schemaManager->tablesExist(self::TEST_TABLE_NAME));
        $table = $this->schemaManager->listTableDetails(self::TEST_TABLE_NAME);
        $columns = $table->getColumns();

        self::assertArrayHasKey(self::TEST_COLUMN_NAME, $columns);
        self::assertArrayHasKey(self::TEST_COLUMN_EXECUTED_AT, $columns);
        self::assertArrayHasKey(self::TEST_COLUMN_EXECUTION_TIME, $columns);
    }

    public function testEnsureInitializedUpdatesTableWhenPresent(): void
    {
        // Sanity check
        self::assertFalse($this->schemaManager->tablesExist(self::TEST_TABLE_NAME));

        $this->createIncompleteMigrationTable();

        self::assertTrue($this->schemaManager->tablesExist(self::TEST_TABLE_NAME));
        self::assertSame([self::TEST_COLUMN_NAME], array_keys($this->schemaManager->listTableColumns(self::TEST_TABLE_NAME)));

        $this->storage->ensureInitialized();

        self::assertTrue($this->schemaManager->tablesExist(self::TEST_TABLE_NAME));
        $table = $this->schemaManager->listTableDetails(self::TEST_TABLE_NAME);
        $columns = $table->getColumns();

        self::assertArrayHasKey(self::TEST_COLUMN_NAME, $columns);
        self::assertSame(191, $columns[self::TEST_COLUMN_NAME]->getLength());
        self::assertArrayHasKey(self::TEST_COLUMN_EXECUTED_AT, $columns);
        self::assertArrayHasKey(self::TEST_COLUMN_EXECUTION_TIME, $columns);
    }

    public function testComplete(): void
    {
        $this->storage->ensureInitialized();
        $migrations = $this->storage->getExecutedMigrations();
        self::assertCount(0, $migrations);
        self::assertFalse($migrations->hasMigration('foo'));

        $this->storage->complete(new ExecutionResult('foo'));

        $migrations = $this->storage->getExecutedMigrations();
        self::assertCount(1, $migrations);
        self::assertTrue($migrations->hasMigration('foo'));
    }

    private function createIncompleteMigrationTable(): void
    {
        $table = new Table(self::TEST_TABLE_NAME);
        $table->addColumn(self::TEST_COLUMN_NAME, 'string', [
            'autoincrement' => true,
            'notnull' => true,
            'length' => 30,
        ]);
        $table->setPrimaryKey([self::TEST_COLUMN_NAME]);
        $this->schemaManager->createTable($table);
    }
}

class_alias(MetadataStorageTest::class, 'Ibexa\Platform\Tests\Migration\Metadata\Storage\MetadataStorageTest');
