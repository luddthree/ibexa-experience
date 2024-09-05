<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Migration\Command;

use Ibexa\Contracts\Core\Persistence\TransactionHandler;
use Ibexa\Contracts\Migration\Exception\MigrationAlreadyExecutedException;
use Ibexa\Contracts\Migration\Exception\MigrationNotFoundException;
use Ibexa\Contracts\Migration\Metadata\Storage\MetadataStorage;
use Ibexa\Contracts\Migration\MigrationService;
use Ibexa\Migration\Metadata\ExecutedMigrationsList;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Lock\LockInterface;

final class MigrateCommand extends Command
{
    private const LOCK_KEY = 'ibexa_migration_lock';
    private const LOCK_TTL = 600;

    protected static $defaultName = 'ibexa:migrations:migrate';

    /** @var \Ibexa\Contracts\Core\Persistence\TransactionHandler */
    private $transactionHandler;

    /** @var \Ibexa\Contracts\Migration\MigrationService */
    private $migrationService;

    /** @var \Ibexa\Contracts\Migration\Metadata\Storage\MetadataStorage */
    private $metadataStorage;

    /** @var \Symfony\Component\Lock\LockFactory */
    private $lockFactory;

    public function __construct(
        TransactionHandler $transactionHandler,
        MetadataStorage $metadataStorage,
        MigrationService $migrationService,
        LockFactory $lockFactory
    ) {
        $this->transactionHandler = $transactionHandler;
        $this->metadataStorage = $metadataStorage;
        $this->migrationService = $migrationService;
        $this->lockFactory = $lockFactory;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setDescription('Execute migrations')
            ->addOption(
                'file',
                null,
                InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                'File with migration steps'
            )
            ->addOption(
                'loader',
                null,
                InputOption::VALUE_REQUIRED,
                '[deprecated] Migration loader to use',
            )
            ->addOption(
                'dry-run',
                null,
                InputOption::VALUE_NONE,
                'Rolls back any database changes'
            )
            ->addOption(
                'user-context',
                null,
                InputOption::VALUE_REQUIRED,
                'User identifier used when performing generation'
            )
            ->addOption(
                'allow-no-migration',
                null,
                InputOption::VALUE_NONE,
                'Do not return failure status code if no migration is available.'
            )
            ->addOption(
                'lock-ttl',
                null,
                InputOption::VALUE_REQUIRED,
                sprintf('TTL should be greater than the longest expected execution time of a single migration file. Defaults to %d seconds', self::LOCK_TTL),
                self::LOCK_TTL
            )
            ->addOption(
                'wait-until-lock',
                null,
                InputOption::VALUE_NEGATABLE,
                'Wait until lock is available, instead of immediately terminating the command and returning the failure status code.',
                false
            )
            ->addOption(
                'disable-locking',
                null,
                InputOption::VALUE_NEGATABLE,
                'Disable locking feature',
                false
            )
        ;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $ttl = $input->getOption('lock-ttl');
        $blocking = $input->getOption('wait-until-lock');

        $isDisableLocking = $input->getOption('disable-locking');
        $lock = null;

        if (!$isDisableLocking) {
            if (!is_numeric($ttl)) {
                $io->error('The parameter lock-ttl must be a number');

                return Command::FAILURE;
            }

            $lock = $this->lockFactory->createLock(self::LOCK_KEY, (float)$ttl);
            $isAcquired = $lock->acquire($blocking);

            if ($isAcquired === false) {
                $io->warning(
                    'Another migration process is still being executed. You can use --disable-locking '
                    . 'option to force command execution, regardless of lock state.'
                );

                return Command::FAILURE;
            }
        }

        if ($input->getOption('loader') !== null) {
            $io->warning('"--loader" option is deprecated without replacement.');
        }

        $userLogin = $input->getOption('user-context');
        $allowNoMigration = $input->getOption('allow-no-migration');
        $isDryRun = $input->getOption('dry-run');
        $paths = $input->getOption('file');

        try {
            return $this->executeMigrations($io, $lock, $allowNoMigration, $isDryRun, $userLogin, $paths);
        } finally {
            $this->releaseIfLocking($lock);
        }
    }

    /**
     * @param array<string>|null $paths
     */
    private function executeMigrations(
        SymfonyStyle $io,
        ?LockInterface $lock,
        bool $allowNoMigration,
        bool $isDryRun,
        ?string $userLogin,
        ?array $paths
    ): int {
        if ($isDryRun) {
            $this->transactionHandler->beginTransaction();
        }

        $this->metadataStorage->ensureInitialized();
        $executedMigrations = $this->metadataStorage->getExecutedMigrations();
        $migrations = empty($paths)
            ? $this->migrationService->listMigrations()
            : $this->getMigrationsByPaths($paths, $executedMigrations);

        if (count($migrations) === 0) {
            if ($allowNoMigration) {
                $io->warning('No migrations found.');

                return Command::SUCCESS;
            }

            $io->error('No migrations found.');

            return Command::FAILURE;
        }

        foreach ($migrations as $migration) {
            if ($executedMigrations->hasMigration($migration->getName())) {
                $io->writeln(sprintf(
                    '⏸  Skipping migration <options=bold>%s</>. It was already executed.',
                    $migration->getName(),
                ));

                continue;
            }

            $io->writeln(sprintf('🚀 Executing migration %s.', $migration->getName()));
            $this->migrationService->executeOne($migration, $userLogin);
            $io->writeln(sprintf('✅ Migration %s finished executing.', $migration->getName()));

            if ($lock !== null) {
                $lock->refresh();
            }
        }
        if ($isDryRun) {
            $this->transactionHandler->rollback();
        }

        $io->success('Migration completed successfully!');

        return Command::SUCCESS;
    }

    private function releaseIfLocking(?LockInterface $lock): void
    {
        if ($lock === null) {
            return;
        }

        $lock->release();
    }

    /**
     * @param string[] $paths
     *
     * @return \Ibexa\Migration\Repository\Migration[]
     */
    private function getMigrationsByPaths(array $paths, ExecutedMigrationsList $executedMigrations): array
    {
        $migrations = [];
        foreach ($paths as $path) {
            $migration = $this->migrationService->findOneByName($path);

            if ($migration === null) {
                throw new MigrationNotFoundException($path);
            }

            if ($executedMigrations->hasMigration($migration->getName())) {
                throw new MigrationAlreadyExecutedException($migration->getName());
            }

            $migrations[] = $migration;
        }

        return $migrations;
    }
}

class_alias(MigrateCommand::class, 'Ibexa\Platform\Bundle\Migration\Command\MigrateCommand');
