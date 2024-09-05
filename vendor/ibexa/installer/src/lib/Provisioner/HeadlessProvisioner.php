<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Installer\Provisioner;

use Doctrine\DBAL\Connection;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

final class HeadlessProvisioner implements ProvisionerInterface
{
    /** @var \Doctrine\DBAL\Connection */
    private $db;

    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    public function provision(OutputInterface $output): void
    {
        $databaseName = $this->db->getDatabasePlatform()->getName();

        $files = $this->getUpdateFiles($databaseName);

        $finder = new Finder();
        $finder
            ->files()
            ->name($files)
            ->in(__DIR__ . '/../../bundle/Resources/install/sql/' . $databaseName);

        foreach ($finder as $sqlFile) {
            $this->runQueriesFromFile($output, $sqlFile->getRealPath());
        }
    }

    /**
     * @param string $databaseName
     *
     * @return string[]
     */
    private function getUpdateFiles(string $databaseName): array
    {
        $files = [
            'mysql' => [
                'user_settings.sql',
            ],
            'postgresql' => [
                'user_settings.sql',
            ],
        ];

        return $files[$databaseName];
    }

    private function runQueriesFromFile(OutputInterface $output, string $file): void
    {
        $queries = array_filter(preg_split('(;\\s*$)m', file_get_contents($file)));

        if (!$output->isQuiet()) {
            $output->writeln(
                sprintf(
                    '<info>Executing %d queries from <comment>%s</comment> on database <comment>%s</comment></info>',
                    count($queries),
                    $file,
                    $this->db->getDatabase(),
                )
            );
        }

        foreach ($queries as $query) {
            $this->db->executeStatement($query);
        }
    }
}
