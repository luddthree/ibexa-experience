<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Migration\Command;

use function file_get_contents;
use Ibexa\Contracts\Migration\MigrationService;
use Ibexa\Migration\Repository\Migration;
use SplFileInfo;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Webmozart\Assert\Assert;

final class ImportCommand extends Command
{
    protected static $defaultName = 'ibexa:migrations:import';

    /** @var \Ibexa\Contracts\Migration\MigrationService */
    private $migrationService;

    /** @var string */
    private $stdin;

    public function __construct(MigrationService $migrationService, string $stdin = 'php://stdin')
    {
        $this->migrationService = $migrationService;
        $this->stdin = $stdin;

        parent::__construct();
    }

    public function setStdin(string $stdin): void
    {
        $this->stdin = $stdin;
    }

    protected function configure(): void
    {
        $this->addArgument(
            'file',
            InputArgument::OPTIONAL,
            'File to import',
        );
        $this->addOption(
            'name',
            null,
            InputOption::VALUE_REQUIRED,
            'Name to assign to migration file',
        );
        $this->addOption(
            'from-stdin',
            null,
            InputOption::VALUE_NONE,
            'Enable reading from standard input',
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        if ($input->getOption('from-stdin')) {
            $migration = $this->createMigrationFromStdin($input, $io);
        } else {
            $migration = $this->createMigrationFromFile($input, $io);
        }

        $this->migrationService->add($migration);

        return 0;
    }

    private function createMigrationFromStdin(InputInterface $input, OutputInterface $output): Migration
    {
        $resource = fopen($this->stdin, 'r');
        Assert::resource($resource);
        $content = stream_get_contents($resource);
        Assert::string($content, 'Could not get the content of standard input');

        $name = $input->getOption('name');
        if ($name === null) {
            throw new RuntimeException('"--name" option is required when using standard input as source');
        }

        $output->writeln(sprintf(
            'Adding migration loaded from standard input with name "%s" to migrations.',
            $name,
        ));

        return new Migration($name, $content);
    }

    private function createMigrationFromFile(InputInterface $input, OutputInterface $output): Migration
    {
        $file = $input->getArgument('file');

        if (empty($file)) {
            throw new InvalidArgumentException(
                'Missing "file" argument. Either provide a file argument, or use --from-stdin option and '
                . 'supply migration contents from standard input.',
            );
        }

        $file = new SplFileInfo($file);

        if (!$file->isFile()) {
            throw new RuntimeException(sprintf('"%s" is not a file.', $file->getPathname()));
        }

        $name = $input->getOption('name') ?? $file->getFilename();
        $content = file_get_contents($file->getPathname());

        if ($content === false) {
            throw new RuntimeException(sprintf('Could not get the content of the file "%s".', $file->getPathname()));
        }

        $output->writeln(sprintf(
            'Adding "%s" with name "%s" to migrations.',
            $file->getPathname(),
            $name,
        ));

        return new Migration($name, $content);
    }
}

class_alias(ImportCommand::class, 'Ibexa\Platform\Bundle\Migration\Command\ImportCommand');
