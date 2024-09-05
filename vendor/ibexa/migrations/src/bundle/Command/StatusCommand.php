<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Migration\Command;

use Ibexa\Contracts\Migration\Metadata\Storage\MetadataStorage;
use Ibexa\Contracts\Migration\MigrationService;
use Ibexa\Migration\Repository\Migration;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class StatusCommand extends Command
{
    protected static $defaultName = 'ibexa:migrations:status';

    private MetadataStorage $metadataStorage;

    private MigrationService $migrationService;

    public function __construct(
        MetadataStorage $metadataStorage,
        MigrationService $migrationService
    ) {
        $this->metadataStorage = $metadataStorage;
        $this->migrationService = $migrationService;

        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->metadataStorage->ensureInitialized();

        $executedMigrations = $this->metadataStorage->getExecutedMigrations();
        $availableMigrations = $this->migrationService->listMigrations();

        $io = new SymfonyStyle($input, $output);
        $io->table(
            [
                'Name',
                'Executed?',
            ],
            array_map(
                static function (Migration $migration) use ($executedMigrations): array {
                    $name = $migration->getName();
                    $isExecuted = $executedMigrations->hasMigration($name);

                    return [
                        $name,
                        sprintf(
                            '<fg=%s>%s</>',
                            $isExecuted ? 'green' : 'red',
                            $isExecuted ? '✓' : '✘',
                        ),
                    ];
                },
                $availableMigrations
            ),
        );

        return 0;
    }
}

class_alias(StatusCommand::class, 'Ibexa\Platform\Bundle\Migration\Command\StatusCommand');
