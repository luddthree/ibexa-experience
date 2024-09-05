<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\Installer;

use Ibexa\Bundle\Core\ApiLoader\RepositoryConfigurationProvider;
use Ibexa\Contracts\Migration\Metadata\Storage\MetadataStorage;
use Ibexa\Contracts\Migration\MigrationService;
use Ibexa\Installer\Executor\CommandExecutor;
use Ibexa\Installer\Output\SkipMigrationMessage;
use Ibexa\Installer\Provisioner\ProvisionerInterface;
use Ibexa\Migration\Repository\Migration;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\KernelInterface;

final class TaxonomyProvisioner implements ProvisionerInterface
{
    private CommandExecutor $commandExecutor;

    private MigrationService $migrationService;

    private MetadataStorage $metadataStorage;

    private KernelInterface $kernel;

    private RepositoryConfigurationProvider $repositoryConfigurationProvider;

    public function __construct(
        CommandExecutor $commandExecutor,
        MigrationService $migrationService,
        MetadataStorage $metadataStorage,
        KernelInterface $kernel,
        RepositoryConfigurationProvider $repositoryConfigurationProvider
    ) {
        $this->commandExecutor = $commandExecutor;
        $this->migrationService = $migrationService;
        $this->metadataStorage = $metadataStorage;
        $this->kernel = $kernel;
        $this->repositoryConfigurationProvider = $repositoryConfigurationProvider;
    }

    public function provision(OutputInterface $output): void
    {
        $connectionName = $this->repositoryConfigurationProvider->getStorageConnectionName();
        $entityManager = "ibexa_{$connectionName}";

        $this->metadataStorage->ensureInitialized();

        $timeout = 0;
        $migrations = [
            '000_taxonomy_content_types.yml' => $this->kernel->locateResource('@IbexaTaxonomyBundle/Resources/install/migrations/content_types.yaml'),
            '001_taxonomy_sections.yml' => $this->kernel->locateResource('@IbexaTaxonomyBundle/Resources/install/migrations/sections.yaml'),
            '002_taxonomy_content.yml' => $this->kernel->locateResource('@IbexaTaxonomyBundle/Resources/install/migrations/content.yaml'),
            '003_taxonomy_permissions.yml' => $this->kernel->locateResource('@IbexaTaxonomyBundle/Resources/install/migrations/permissions.yaml'),
        ];

        $this->commandExecutor->executeCommand(
            $output,
            sprintf('doctrine:schema:update --dump-sql --force --em=%s', $entityManager),
            $timeout
        );
        foreach ($migrations as $name => $path) {
            $migration = new Migration($name, file_get_contents($path));
            $this->migrationService->add($migration);

            if ($this->migrationService->isMigrationExecuted($migration)) {
                $output->writeln(SkipMigrationMessage::createMessage($migration));

                continue;
            }

            $this->migrationService->executeOne($migration);
        }
    }
}
