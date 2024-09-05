<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\Dumper;

use Ibexa\Migration\Log\LoggerAwareTrait;
use League\Flysystem\FilesystemOperator;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use RuntimeException;

final class MigrationDumper implements MigrationDumperInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    private FilesystemOperator $filesystem;

    /** @var resource */
    private $stream;

    /** @var string */
    private $migrationsSubdir;

    public function __construct(
        FilesystemOperator $filesystem,
        string $migrationsSubdir = 'migrations',
        ?LoggerInterface $logger = null
    ) {
        $this->filesystem = $filesystem;
        $this->migrationsSubdir = $migrationsSubdir;
        $this->logger = $logger ?? new NullLogger();
    }

    /**
     * @param string[] $data
     *
     * @throws \RuntimeException
     */
    public function dump(iterable $data, MigrationFile $migrationFile): void
    {
        $this->getLogger()->notice(sprintf('Dumping migrations to file %s', $migrationFile->getFilename()));

        $this->openStream();
        $this->writeIntoStream($data);
        $this->filesystem->writeStream($this->migrationsSubdir . '/' . $migrationFile->getFilename(), $this->stream);
        $this->closeStream();
    }

    /**
     * @throws \RuntimeException
     */
    private function openStream(): void
    {
        $this->stream = fopen('php://temp', 'r+');
        self::validResult($this->stream, 'Cannot open stream');
    }

    /**
     * @throws \RuntimeException
     */
    private function writeIntoStream(iterable $data): void
    {
        foreach ($data as $encodedData) {
            $result = fwrite($this->stream, $encodedData);
            self::validResult($result, 'Cannot write into stream');
        }
    }

    private function closeStream(): void
    {
        fclose($this->stream);
    }

    /**
     * @throws \RuntimeException
     */
    private static function validResult($result, string $message): void
    {
        if (false === $result) {
            $commonMessage = 'Failed data dump attempt';
            throw new RuntimeException(
                sprintf('%s - %s', $commonMessage, $message)
            );
        }
    }
}

class_alias(MigrationDumper::class, 'Ibexa\Platform\Migration\Dumper\MigrationDumper');
