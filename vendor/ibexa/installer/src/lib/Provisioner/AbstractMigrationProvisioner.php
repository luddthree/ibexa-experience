<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Installer\Provisioner;

use Ibexa\Contracts\Migration\MigrationService;
use Ibexa\Installer\Output\SkipMigrationMessage;
use Ibexa\Migration\Repository\Migration;
use RuntimeException;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\KernelInterface;

abstract class AbstractMigrationProvisioner implements ProvisionerInterface
{
    private MigrationService $migrationService;

    private KernelInterface $kernel;

    public function __construct(MigrationService $migrationService, KernelInterface $kernel)
    {
        $this->migrationService = $migrationService;
        $this->kernel = $kernel;
    }

    /**
     * @return array<string,string>
     */
    abstract protected function getMigrationFiles(): array;

    abstract protected function getMigrationDirectory(): string;

    final public function provision(OutputInterface $output): void
    {
        foreach ($this->getMigrationFiles() as $name => $filename) {
            $migration = new Migration($name, $this->getMigrationContent($filename));

            $this->migrationService->add($migration);
            if ($this->migrationService->isMigrationExecuted($migration)) {
                $output->writeln(SkipMigrationMessage::createMessage($migration));
                continue;
            }

            $this->migrationService->executeOne($migration);
        }
    }

    /**
     * @throws \RuntimeException
     */
    private function getMigrationContent(string $name): string
    {
        $filename = $this->kernel->locateResource($this->getMigrationDirectory() . '/' . $name);

        $content = file_get_contents($filename);
        if ($content === false) {
            throw new RuntimeException(sprintf('Unable to read "%s" migration file', $filename));
        }

        return $content;
    }
}
