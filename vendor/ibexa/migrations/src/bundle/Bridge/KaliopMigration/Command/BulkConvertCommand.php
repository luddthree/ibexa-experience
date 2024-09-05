<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Migration\Bridge\KaliopMigration\Command;

use Doctrine\Common\Collections\ArrayCollection;
use Ibexa\Migration\Log\LoggerAwareTrait;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use League\Flysystem\FileAttributes;
use League\Flysystem\FilesystemException as FlysystemException;
use League\Flysystem\FilesystemOperator;
use League\Flysystem\StorageAttributes;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\RuntimeException as ConsoleRuntimeException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Serializer\SerializerInterface;
use Throwable;
use Webmozart\Assert\Assert;

final class BulkConvertCommand extends Command implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    protected static $defaultName = 'ibexa:migrations:kaliop:bulk-convert';

    /** @var \Symfony\Component\Serializer\SerializerInterface */
    private $serializer;

    private FilesystemOperator $filesystem;

    /** @var \Symfony\Component\Console\Style\SymfonyStyle */
    private $io;

    /** @var bool */
    private $recursive;

    /** @var string */
    private $inputDir;

    /** @var string|null */
    private $outputDir;

    /** @var bool */
    private $updateInPlace;

    /** @var bool */
    private $allowOutputOverwrite;

    /** @var bool */
    private $allowBackupOverwrite;

    /** @var bool */
    private $backup;

    /** @var bool */
    private $continueOnError;

    /** @var bool */
    private $skipExisting;

    /** @var bool */
    private $discardInvalidSteps;

    /** @var string|null */
    private $defaultLanguage;

    /** @var array<string, Throwable[]> */
    private $errors;

    public function __construct(
        SerializerInterface $serializer,
        FilesystemOperator $filesystem,
        ?LoggerInterface $logger = null
    ) {
        $this->logger = $logger ?? new NullLogger();
        $this->serializer = $serializer;
        $this->filesystem = $filesystem;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument(
            'input-directory',
            InputArgument::REQUIRED,
            'Location of kaliop migration files',
        );
        $this->addArgument(
            'output-directory',
            InputArgument::OPTIONAL,
            'Output directory. If not specified, files will be replaced',
        );
        $this->addOption(
            'recursive',
            'R',
            InputOption::VALUE_NONE,
            'Recursively traverse filesystem'
        );
        $this->addOption(
            'no-backup',
            null,
            InputOption::VALUE_NONE,
            'Disables creating file backups when converting without output directory',
        );
        $this->addOption(
            'backup-overwrite',
            'B',
            InputOption::VALUE_NONE,
            'Allows overwriting of backup files',
        );
        $this->addOption(
            'output-overwrite',
            'O',
            InputOption::VALUE_NONE,
            'Allows overwriting of output files',
        );
        $this->addOption(
            'continue-on-error',
            'C',
            InputOption::VALUE_NONE,
            'Files that cannot be converted will not stop the command execution'
        );
        $this->addOption(
            'skip-existing',
            'E',
            InputOption::VALUE_NONE,
            'Skip conversion if converted result file already exists'
        );
        $this->addOption(
            'discard-invalid-steps',
            'D',
            InputOption::VALUE_NONE,
            'Ignore invalid steps when converting'
        );
        $this->addOption(
            'default-language',
            'L',
            InputOption::VALUE_REQUIRED,
            'Sets default language code e.g. fre-FR'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io = new SymfonyStyle($input, $output);

        $this->recursive = $input->getOption('recursive');
        $this->inputDir = $input->getArgument('input-directory');
        $this->outputDir = $input->getArgument('output-directory');
        $this->updateInPlace = $this->outputDir === null || $this->outputDir === $this->inputDir;
        $this->allowOutputOverwrite = $input->getOption('output-overwrite');
        $this->allowBackupOverwrite = $input->getOption('backup-overwrite');
        $this->backup = $this->updateInPlace && false === $input->getOption('no-backup');
        $this->continueOnError = $input->getOption('continue-on-error');
        $this->skipExisting = $input->getOption('skip-existing');
        $this->discardInvalidSteps = $input->getOption('discard-invalid-steps');
        $this->defaultLanguage = $input->getOption('default-language');

        $filesStartedCounter = 0;
        $filesProcessedCounter = 0;
        /** @var string[] $processedFiles */
        $processedFiles = [];

        $files = $this->getFilesToConvert();
        foreach ($files as $file) {
            ++$filesStartedCounter;

            $inputFilepath = $file['path'];
            $result = $this->processSingleFile($file);
            if ($result) {
                ++$filesProcessedCounter;
            }

            $processedFiles[] = $inputFilepath;
        }

        if ($filesStartedCounter === 0) {
            $this->io->warning('No YAML files found in specified directory');

            return 1;
        }

        $this->displayFilesWithStatus($processedFiles);

        if ($filesStartedCounter === $filesProcessedCounter) {
            $this->io->success('Finished converting successfully');
            $this->io->writeln([
                '',
                sprintf('Processed %d files', $filesStartedCounter),
                sprintf('<fg=green>✓</> %d OK', $filesProcessedCounter),
            ]);

            return 0;
        } else {
            $this->io->warning('Finished converting with errors');
            $errorsGroupedByMessage = $this->getErrorsGroupedByMessage();
            foreach ($errorsGroupedByMessage as $message => $errors) {
                $this->io->writeln(sprintf(' * [%d] %s', count($errors), $message));
            }

            $this->io->writeln([
                '',
                sprintf('Processed %d files', $filesStartedCounter),
                sprintf('<fg=green>✓</> %d OK', $filesProcessedCounter),
                sprintf('<fg=red>✘</> %d FAILED', $filesStartedCounter - $filesProcessedCounter),
            ]);

            if ($input->isInteractive()) {
                $this->askWhichExceptionToDisplay($errorsGroupedByMessage);
            }

            return 1;
        }
    }

    private function handleConversion(string $content, string $inputFilepath, string $outputFilepath): string
    {
        $errorsCollection = new ArrayCollection();
        $context = [
            'errors_collection' => $errorsCollection,
            'discard_invalid_steps' => $this->discardInvalidSteps,
            'default_language' => $this->defaultLanguage,
            'output' => $outputFilepath,
        ];
        $steps = $this->serializer->deserialize($content, StepInterface::class . '[]', 'yaml', $context);
        $steps = iterator_to_array($steps);

        if (!$errorsCollection->isEmpty()) {
            $this->errors[$inputFilepath] = $errorsCollection->toArray();
        }

        return $this->serializer->serialize($steps, 'yaml');
    }

    /**
     * @throws \League\Flysystem\FilesystemException
     */
    private function handleBackup(string $outputFilepath, string $content): void
    {
        $backupFilepath = $outputFilepath . '.backup';
        if (!$this->allowBackupOverwrite && $this->filesystem->fileExists($backupFilepath)) {
            throw new ConsoleRuntimeException(
                "Backup file $backupFilepath exists. Run this command with --backup-overwrite option to overwrite it."
            );
        }

        $this->filesystem->write($backupFilepath, $content);
    }

    /**
     * @throws \League\Flysystem\FilesystemException
     */
    private function handleSavingAfterConversion(
        string $serialized,
        string $outputFilepath
    ): void {
        if (
            !$this->updateInPlace &&
            !$this->allowOutputOverwrite &&
            $this->filesystem->fileExists($outputFilepath)
        ) {
            throw new ConsoleRuntimeException(
                "Output file $outputFilepath exists. Run this command with --output-overwrite option to overwrite it."
            );
        }

        $this->filesystem->write($outputFilepath, $serialized);
    }

    private function pickOutputFilePath(FileAttributes $file): string
    {
        $filename = basename($file->path());
        if ($this->recursive) {
            $pathComponents = [];
            if ($this->outputDir !== null && $this->outputDir !== '') {
                $pathComponents[] = rtrim($this->outputDir, '/');
            }

            $dirname = dirname($file->path());
            $inputDir = rtrim($this->inputDir, '/');
            if ($dirname !== '') {
                $dirname = substr($dirname, strlen($inputDir));
                $dirname = ltrim($dirname, '/');

                $pathComponents[] = $dirname;
            }

            return sprintf(
                '%s/%s',
                implode('/', $pathComponents),
                $filename
            );
        }

        return sprintf(
            '%s/%s',
            rtrim($this->outputDir ?? $this->inputDir, '/'),
            $filename
        );
    }

    /**
     * @return iterable<FileAttributes>
     *
     * @throws \League\Flysystem\FilesystemException
     */
    private function getFilesToConvert(): iterable
    {
        $files = $this
            ->filesystem
            ->listContents($this->inputDir, $this->recursive)
            ->filter(
                fn (StorageAttributes $attributes): bool => $attributes->isFile()
                    && in_array($this->getFileExtension($attributes), ['yml', 'yaml'], true)
            );

        if ($this->skipExisting) {
            $files = $files->filter(
                function (FileAttributes $file): bool {
                    $outputFilepath = $this->pickOutputFilePath($file);

                    return false === $this->filesystem->fileExists($outputFilepath);
                }
            );
        }

        return $files->sortByPath();
    }

    /**
     * @throws \League\Flysystem\FilesystemException
     */
    private function processSingleFile(FileAttributes $file): bool
    {
        $inputFilepath = $file->path();
        $this->io->writeln(sprintf('Converting: "%s"', $inputFilepath), OutputInterface::VERBOSITY_VERBOSE);

        $outputFilepath = $this->pickOutputFilePath($file);

        $content = $this->filesystem->read($inputFilepath);
        Assert::string($content, sprintf('"%s" is not readable', $inputFilepath));

        try {
            $serialized = $this->handleConversion($content, $inputFilepath, $outputFilepath);
            if ($this->backup) {
                $this->handleBackup($outputFilepath, $content);
            }

            $this->handleSavingAfterConversion($serialized, $outputFilepath);

            return empty($this->errors[$inputFilepath]);
        } catch (FlysystemException $e) {
            throw $e;
        } catch (Throwable $e) {
            $message = sprintf(
                'Converting file "%s" failed. %s',
                $inputFilepath,
                $e->getMessage(),
            );
            $exception = new RuntimeException($message, $e->getCode(), $e);
            $this->errors[$inputFilepath] = [$e];

            if ($this->continueOnError) {
                $this->getLogger()->error($message, [
                    'exception' => $exception,
                ]);
            } else {
                throw $exception;
            }
        }

        return false;
    }

    /**
     * @param string[] $results
     */
    private function displayFilesWithStatus(array $results): void
    {
        $convertToString = function (string $file): string {
            $status = !isset($this->errors[$file]);

            return sprintf(
                '<fg=%s>%s</> %s',
                $status ? 'green' : 'red',
                $status === true ? '✓' : '✘',
                $file,
            );
        };

        $this->io->listing(
            array_map(
                $convertToString,
                $results
            )
        );
    }

    /**
     * Returns Throwables grouped by message, sorted by exception count, descending.
     *
     * @return \Throwable[][]
     */
    private function getErrorsGroupedByMessage(): array
    {
        $errorGroupingReducer = static function (array $carry, array $errors): array {
            foreach ($errors as $error) {
                if ($previous = $error->getPrevious()) {
                    $error = $previous;
                }

                $message = sprintf('[%s] %s', get_class($error), $error->getMessage());

                if (!isset($carry[$message])) {
                    $carry[$message] = [];
                }

                $carry[$message][] = $error;
            }

            return $carry;
        };

        $groupedErrors = array_reduce($this->errors, $errorGroupingReducer, []);

        uasort($groupedErrors, static function (array $errorsA, array $errorsB): int {
            // sort in reverse order
            return count($errorsB) <=> count($errorsA);
        });

        return $groupedErrors;
    }

    /**
     * @param \Throwable[][] $errorsGroupedByMessage
     */
    private function askWhichExceptionToDisplay(array $errorsGroupedByMessage): void
    {
        $firstExceptionSelector = static function (array $errors): string {
            $firstError = $errors[0];

            return sprintf(
                '[%s] %s [%d]',
                get_class($firstError),
                $firstError->getMessage(),
                count($errors),
            );
        };
        $choices = array_values(array_map($firstExceptionSelector, $errorsGroupedByMessage));
        $question = new ChoiceQuestion('Display exception (enter to end)', $choices, '');
        $question->setValidator(static function ($answer) use ($choices) {
            if ($answer === '') {
                return $answer;
            }

            if (is_numeric($answer) && isset($choices[($answer)])) {
                return $answer;
            }

            $result = array_search($answer, $choices);

            if ($result !== false && isset($choices[$result])) {
                return $result;
            }

            throw new \InvalidArgumentException('Invalid answer');
        });
        $question->setAutocompleterValues(array_keys($choices));

        while ('' !== $answer = $this->io->askQuestion($question)) {
            $errors = array_values($errorsGroupedByMessage);
            $this->io->writeln((string)($errors[$answer][0]));
        }
    }

    private function getFileExtension(StorageAttributes $file): ?string
    {
        if (preg_match('/\.([^.]+)$/', $file->path(), $matches)) {
            return $matches[1];
        }

        return null;
    }
}

class_alias(BulkConvertCommand::class, 'Ibexa\Platform\Bundle\Migration\Bridge\KaliopMigration\Command\BulkConvertCommand');
