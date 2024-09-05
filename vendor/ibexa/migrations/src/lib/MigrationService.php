<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration;

use function array_filter;
use function count;
use DateTimeImmutable;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\Migration\Exception\MigrationAlreadyExecutedException;
use Ibexa\Contracts\Migration\Metadata\Storage\MetadataStorage;
use Ibexa\Contracts\Migration\MigrationExecutor as MigrationExecutorInterface;
use Ibexa\Contracts\Migration\MigrationService as MigrationServiceInterface;
use Ibexa\Contracts\Migration\MigrationStorage;
use Ibexa\Migration\Log\LoggerAwareTrait;
use Ibexa\Migration\Metadata\ExecutionResult;
use Ibexa\Migration\Repository\Migration;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use function sprintf;
use Symfony\Component\Stopwatch\Stopwatch;

final class MigrationService implements MigrationServiceInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    private const IBEXA_MIGRATIONS_STOPWATCH_EVENT_NAME = 'ibexa.migrations.execute';

    /** @var \Ibexa\Contracts\Migration\MigrationStorage */
    private $storage;

    /** @var \Ibexa\Contracts\Migration\Metadata\Storage\MetadataStorage */
    private $metadataStorage;

    /** @var \Ibexa\Contracts\Migration\MigrationExecutor */
    private $executor;

    /** @var \Ibexa\Contracts\Core\Repository\UserService */
    private $userService;

    /** @var \Ibexa\Contracts\Core\Repository\PermissionResolver */
    private $permissionResolver;

    /** @var \Symfony\Component\Stopwatch\Stopwatch */
    private $stopwatch;

    /** @var string */
    private $defaultUserLogin;

    /** @var \Ibexa\Contracts\Core\Repository\Values\User\UserReference|null */
    private $previousApiUser = null;

    public function __construct(
        MigrationStorage $storage,
        MetadataStorage $metadataStorage,
        MigrationExecutorInterface $executor,
        UserService $userService,
        PermissionResolver $permissionResolver,
        Stopwatch $stopwatch,
        string $defaultUserLogin,
        ?LoggerInterface $logger = null
    ) {
        $this->storage = $storage;
        $this->metadataStorage = $metadataStorage;
        $this->executor = $executor;
        $this->userService = $userService;
        $this->permissionResolver = $permissionResolver;
        $this->stopwatch = $stopwatch;
        $this->defaultUserLogin = $defaultUserLogin;
        $this->logger = $logger ?? new NullLogger();
    }

    public function findOneByName(string $name, ?string $loader = null): ?Migration
    {
        return $this->storage->loadOne($name);
    }

    public function add(Migration $migration): void
    {
        $this->storage->store($migration);
    }

    /**
     * @return \Ibexa\Migration\Repository\Migration[]
     */
    public function listMigrations(): array
    {
        $migrations = $this->storage->loadAll();

        usort($migrations, static function (Migration $migrationA, Migration $migrationB): int {
            return $migrationA->getName() <=> $migrationB->getName();
        });

        return $migrations;
    }

    public function isMigrationExecuted(Migration $migration): bool
    {
        $executedMigrations = $this->metadataStorage->getExecutedMigrations();
        $migrationName = $migration->getName();

        return $executedMigrations->hasMigration($migrationName);
    }

    public function executeOne(Migration $migration, ?string $userLogin = null): void
    {
        $this->setupUserContext($userLogin);

        try {
            $this->doExecuteMigration($migration);
        } finally {
            $this->restoreUserContext();
        }
    }

    public function executeAll(?string $userLogin = null): void
    {
        $this->setupUserContext($userLogin);

        $stopwatchEvent = $this->stopwatch->start(self::IBEXA_MIGRATIONS_STOPWATCH_EVENT_NAME);

        try {
            $migrations = $this->getAvailableMigrations();
            foreach ($migrations as $migration) {
                $this->doExecuteMigration($migration);
            }

            $this->getLogger()->notice(
                'finished in {duration}ms, {migrations_count} migrations executed',
                [
                    'duration' => $stopwatchEvent->getDuration(),
                    'migrations_count' => count($migrations),
                ]
            );
        } finally {
            $this->restoreUserContext();
        }
    }

    private function setupUserContext(?string $userLogin = null): void
    {
        $userIdentifier = $userLogin ?? $this->defaultUserLogin;
        $user = $this->userService->loadUserByLogin($userIdentifier);

        $this->previousApiUser = $this->permissionResolver->getCurrentUserReference();
        $this->permissionResolver->setCurrentUserReference($user);
    }

    private function restoreUserContext(): void
    {
        $this->permissionResolver->setCurrentUserReference($this->previousApiUser);
        $this->previousApiUser = null;
    }

    /**
     * @throws \Ibexa\Contracts\Migration\Exception\MigrationAlreadyExecutedException when migration is already present in metadata storage.
     */
    private function assertMigrationNotExecuted(Migration $migration): void
    {
        if ($this->isMigrationExecuted($migration)) {
            throw new MigrationAlreadyExecutedException($migration->getName());
        }
    }

    private function doExecuteMigration(Migration $migration): void
    {
        $migrationName = $migration->getName();
        $this->getLogger()->notice(sprintf('Executing migration: "%s"', $migrationName));

        $stopwatchEvent = $this->stopwatch->start('ibexa.migration.execute');

        $this->assertMigrationNotExecuted($migration);

        $this->executor->execute($migration);

        $stopwatchEvent->stop();
        $periods = $stopwatchEvent->getPeriods();
        $lastPeriod = $periods[count($periods) - 1];

        $result = new ExecutionResult($migrationName);

        $result->setTime($lastPeriod->getDuration());
        $result->setMemory($lastPeriod->getMemory());
        $result->setExecutedAt(new DateTimeImmutable());

        $this->metadataStorage->complete($result);
    }

    /**
     * Prepares list of migrations that have not been executed yet.
     *
     * @return \Ibexa\Migration\Repository\Migration[]
     */
    private function getAvailableMigrations(): array
    {
        $availableMigrations = $this->listMigrations();
        $executedMigrations = $this->metadataStorage->getExecutedMigrations();

        return array_filter(
            $availableMigrations,
            static function (Migration $availableMigration) use ($executedMigrations): bool {
                foreach ($executedMigrations->getItems() as $executedMigration) {
                    if ($executedMigration->getName() === $availableMigration->getName()) {
                        return false;
                    }
                }

                return true;
            },
        );
    }
}

class_alias(MigrationService::class, 'Ibexa\Platform\Migration\MigrationService');
