<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\Storage;

use Ibexa\Contracts\Migration\Exception\MigrationAlreadyExistsException;
use Ibexa\Contracts\Migration\MigrationStorage;
use Ibexa\Migration\Repository\Migration;
use League\Flysystem\FilesystemOperator;
use League\Flysystem\UnableToReadFile;
use function md5;

final class FlysystemMigrationStorage implements MigrationStorage
{
    private FilesystemOperator $filesystem;

    /** @var string */
    private $migrationsSubdir;

    public function __construct(FilesystemOperator $filesystem, string $migrationsSubdir = 'migrations')
    {
        $this->filesystem = $filesystem;
        $this->migrationsSubdir = rtrim($migrationsSubdir, '/');
    }

    public function loadOne(string $name): ?Migration
    {
        try {
            $content = $this->filesystem->read($this->migrationsSubdir . '/' . $name);
        } catch (UnableToReadFile $e) {
            return null;
        }

        return new Migration($name, $content);
    }

    public function loadAll(): array
    {
        $contents = $this->filesystem->listContents($this->migrationsSubdir, true);

        $migrations = [];
        foreach ($contents as $content) {
            if ($content['type'] !== 'file') {
                continue;
            }

            $name = $path = $content['path'];

            if (substr($path, 0, strlen($this->migrationsSubdir)) === $this->migrationsSubdir) {
                $name = substr($path, strlen($this->migrationsSubdir));
                $name = ltrim($name, '/');
            }

            $migrations[] = new Migration($name, $this->filesystem->read($path));
        }

        return $migrations;
    }

    public function store(Migration $migration): void
    {
        $path = $this->migrationsSubdir . '/' . $migration->getName();

        if ($this->filesystem->fileExists($path)) {
            $existingContent = $this->filesystem->read($path);

            if (empty($existingContent)) {
                throw MigrationAlreadyExistsException::failedReadFallback($migration);
            }

            if (md5($existingContent) === md5($migration->getContent())) {
                return;
            }

            throw MigrationAlreadyExistsException::new($migration);
        }

        $this->filesystem->write($path, $migration->getContent());
    }
}

class_alias(FlysystemMigrationStorage::class, 'Ibexa\Platform\Migration\Storage\FlysystemMigrationStorage');
