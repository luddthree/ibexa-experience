<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Migration\Command;

use Ibexa\Contracts\Migration\Exception\InvalidMigrationException;
use Ibexa\Contracts\Migration\Exception\MigrationAlreadyExecutedException;
use Ibexa\Contracts\Migration\Exception\MigrationNotFoundException;
use Ibexa\Migration\Metadata\ExecutionResult;

/**
 * @covers \Ibexa\Bundle\Migration\Command\MigrateCommand
 */
class MigrateCommandTest extends AbstractCommandTest
{
    protected static function getCommandName(): string
    {
        return 'ibexa:migrations:migrate';
    }

    protected function setUp(): void
    {
        parent::setUp();
        self::getMetadataStorage()->ensureInitialized();
    }

    public function testFileOptionIsOptional(): void
    {
        $filesystem = self::getFilesystem();
        $filesystem->write('migrations/foo.test', '[]');
        $filesystem->write('migrations/bar.test', '[]');

        $this->commandTester->execute([]);
        self::assertSame(0, $this->commandTester->getStatusCode());
    }

    public function testMigrationStopsOnFirstInvalidMigrationFile(): void
    {
        $filesystem = self::getFilesystem();
        $filesystem->write('migrations/foo.test', '[]');
        $filesystem->write('migrations/bar.test', '');

        self::expectException(InvalidMigrationException::class);
        self::expectExceptionMessage('Invalid migration data.');
        $this->commandTester->execute([]);
    }

    public function testMigrationFailsWithNonExistentFile(): void
    {
        self::expectException(MigrationNotFoundException::class);
        self::expectExceptionMessage('Migration "foo.test" not found.');
        $this->commandTester->execute([
            '--file' => ['foo.test'],
        ]);
    }

    public function testMigrationFailsWhenTriedToExecuteAsSecondTime(): void
    {
        $filesystem = self::getFilesystem();
        $filesystem->write('migrations/foo.test', '[]');
        $this->commandTester->execute([
            '--file' => ['foo.test'],
        ]);

        self::expectException(MigrationAlreadyExecutedException::class);
        self::expectExceptionMessage('"foo.test" migration is already executed.');

        $this->commandTester->execute([
            '--file' => ['foo.test'],
        ]);
    }

    public function testMigrationMultipleMigrationFilesSpecified(): void
    {
        self::assertCount(0, self::getMetadataStorage()->getExecutedMigrations());

        $filesystem = self::getFilesystem();
        $filesystem->write('migrations/foo.test', '[]');
        $filesystem->write('migrations/bar.test', '[]');
        $this->commandTester->execute([
            '--file' => [
                'foo.test',
                'bar.test',
            ],
        ]);

        self::assertSame(0, $this->commandTester->getStatusCode());
        self::assertCount(2, self::getMetadataStorage()->getExecutedMigrations());
    }

    public function testMigrationFailsWithoutAnyMigrations(): void
    {
        $this->commandTester->execute([]);
        self::assertSame(1, $this->commandTester->getStatusCode());
    }

    public function testMigrationSucceedsWhenNoMigrationToExecuteButSomeWereCompletedEarlier(): void
    {
        self::getMetadataStorage()->complete(new ExecutionResult('foo.test'));
        $filesystem = self::getFilesystem();
        $filesystem->write('migrations/foo.test', '[]');

        $this->commandTester->execute([]);
        self::assertSame(0, $this->commandTester->getStatusCode());
    }

    public function testMigrationFailsWithoutAnyMigrationsEvenIfSomeWereCompletedEarlier(): void
    {
        // Even if migration were previously executed, if filesystem is empty this suggests an error
        self::getMetadataStorage()->complete(new ExecutionResult('foo.test'));

        $this->commandTester->execute([]);
        self::assertSame(1, $this->commandTester->getStatusCode());
    }

    public function testMigrationSuccessWithoutAnyMigrationsAndWithAllowNoMigrationOption(): void
    {
        $this->commandTester->execute([
            '--allow-no-migration' => true,
        ]);
        self::assertSame(0, $this->commandTester->getStatusCode());
    }
}

class_alias(MigrateCommandTest::class, 'Ibexa\Platform\Tests\Bundle\Migration\Command\MigrateCommandTest');
