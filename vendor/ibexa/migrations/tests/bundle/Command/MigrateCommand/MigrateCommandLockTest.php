<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Migration\Command\MigrateCommand;

use Ibexa\Core\Repository\Values\User\UserReference;
use Ibexa\Tests\Bundle\Migration\Command\AbstractCommandTest;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Lock\Store\DoctrineDbalStore;

/**
 * @covers \Ibexa\Bundle\Migration\Command\MigrateCommand
 */
final class MigrateCommandLockTest extends AbstractCommandTest
{
    private const FIXTURE_ADMIN_ID = 14;
    private const LOCK_TTL = 3;
    private const LOCK_KEY = 'ibexa_migration_lock';

    protected static function getCommandName(): string
    {
        return 'ibexa:migrations:migrate';
    }

    protected function setUp(): void
    {
        parent::setUp();
        self::getPermissionResolver()->setCurrentUserReference(new UserReference(self::FIXTURE_ADMIN_ID));
    }

    public function testNoWaitLockMigration(): void
    {
        $factory = new LockFactory($this->geLockStore());
        $lock = $factory->createLock(self::LOCK_KEY, self::LOCK_TTL);
        $lock->acquire();
        $commandInput = [
            '--allow-no-migration' => true,
        ];

        $this->commandTester->execute($commandInput);

        self::assertSame(Command::FAILURE, $this->commandTester->getStatusCode());

        $lock->release();
        $this->commandTester->execute($commandInput);

        self::assertSame(Command::SUCCESS, $this->commandTester->getStatusCode());
    }

    public function testWaitLockMigration(): void
    {
        $factory = new LockFactory($this->geLockStore());
        $lock = $factory->createLock(self::LOCK_KEY, self::LOCK_TTL);
        $lock->acquire(true);
        $commandInput = [
            '--wait-until-lock' => null,
            '--allow-no-migration' => true,
        ];

        $this->commandTester->execute($commandInput);

        self::assertSame(Command::SUCCESS, $this->commandTester->getStatusCode());
    }

    public function testIncorrectTTLMigration(): void
    {
        $commandInput = [
            '--lock-ttl' => 'test',
            '--allow-no-migration' => true,
        ];

        $this->commandTester->execute($commandInput);

        self::assertSame(Command::FAILURE, $this->commandTester->getStatusCode());
    }

    public function testCorrectTTLMigration(): void
    {
        $commandInput = [
            '--lock-ttl' => 5,
            '--allow-no-migration' => true,
        ];

        $this->commandTester->execute($commandInput);

        self::assertSame(Command::SUCCESS, $this->commandTester->getStatusCode());
    }

    public function testDisableLockingMigration(): void
    {
        $commandInput = [
            '--lock-ttl' => 5,
            '--wait-until-lock' => null,
            '--disable-locking' => true,
            '--allow-no-migration' => true,
        ];

        $factory = new LockFactory($this->geLockStore());
        $lock = $factory->createLock(self::LOCK_KEY, self::LOCK_TTL);
        $lock->acquire(true);

        $this->commandTester->execute($commandInput);

        self::assertSame(Command::SUCCESS, $this->commandTester->getStatusCode());
    }

    protected static function geLockStore(): DoctrineDbalStore
    {
        return self::getServiceByClassName(DoctrineDbalStore::class, 'ibexa.migrations.lock_store');
    }
}
