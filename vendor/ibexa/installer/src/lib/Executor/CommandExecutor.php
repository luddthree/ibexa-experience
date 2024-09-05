<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Installer\Executor;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;

class CommandExecutor
{
    private const VERBOSITY_MAP = [
        OutputInterface::VERBOSITY_QUIET => '-q',
        OutputInterface::VERBOSITY_VERBOSE => '-v',
        OutputInterface::VERBOSITY_VERY_VERBOSE => '-vv',
        OutputInterface::VERBOSITY_DEBUG => '-vvv',
    ];

    /** @var string */
    protected $environment;

    public function __construct(string $environment)
    {
        $this->environment = $environment;
    }

    public function executeCommand(OutputInterface $output, string $cmd, int $timeout = 300): void
    {
        $phpFinder = new PhpExecutableFinder();
        if (!$phpPath = $phpFinder->find(false)) {
            throw new \RuntimeException('The php executable could not be found, add it to your PATH environment variable and try again');
        }

        // We don't know which php arguments where used so we gather some to be on the safe side
        $arguments = $phpFinder->findArguments();
        if (false !== ($ini = php_ini_loaded_file())) {
            $arguments[] = '--php-ini=' . $ini;
        }

        // Pass memory_limit in case this was specified as php argument, if not it will most likely be same as $ini.
        if ($memoryLimit = ini_get('memory_limit')) {
            $arguments[] = '-d memory_limit=' . $memoryLimit;
        }

        $phpArgs = implode(' ', array_map('escapeshellarg', $arguments));
        $php = escapeshellarg($phpPath) . ($phpArgs ? ' ' . $phpArgs : '');

        // Make sure to pass along relevant global Symfony options to console command
        $console = escapeshellarg('bin/console');

        if (isset(self::VERBOSITY_MAP[$output->getVerbosity()])) {
            $console .= ' ' . self::VERBOSITY_MAP[$output->getVerbosity()];
        }

        if ($output->isDecorated()) {
            $console .= ' --ansi';
        }

        $console .= ' --env=' . escapeshellarg($this->environment);

        $process = Process::fromShellCommandline($php . ' ' . $console . ' ' . $cmd, null, null, null, $timeout);
        $process->run(
            static function ($type, $buffer) use ($output): void {
                $output->write($buffer, false);
            }
        );

        if (!$process->isSuccessful()) {
            throw new \RuntimeException(
                sprintf('An error occurred when executing the "%s" command.', escapeshellarg($cmd))
            );
        }
    }
}

class_alias(CommandExecutor::class, 'Ibexa\Platform\Installer\Executor\CommandExecutor');
