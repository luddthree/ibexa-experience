<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\File;

use Ibexa\Personalization\Exception\FileNotFoundException;
use Ibexa\Personalization\Exception\ReadFileContentFailedException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Filesystem as BaseFilesystem;

final class FileManager implements FileManagerInterface
{
    private const LOCK_FILE_NAME = '.lock';

    private Filesystem $filesystem;

    private string $exportDocumentRoot;

    public function __construct(
        BaseFilesystem $filesystem,
        string $exportDocumentRoot
    ) {
        $this->filesystem = $filesystem;
        $this->exportDocumentRoot = $exportDocumentRoot;
    }

    /**
     * @throws \Ibexa\Personalization\Exception\FileNotFoundException
     */
    public function load(string $file): ?string
    {
        $filePath = $this->getDir() . $file;
        if (!$this->filesystem->exists($filePath)) {
            throw new FileNotFoundException(sprintf('File: %s not found.', $file));
        }

        $content = file_get_contents($filePath);
        if (false === $content) {
            throw new ReadFileContentFailedException($filePath);
        }

        return $content;
    }

    public function save(string $file, string $content): void
    {
        $this->filesystem->dumpFile($file, $content);
    }

    public function getDir(): string
    {
        return $this->exportDocumentRoot;
    }

    public function createChunkDir(): string
    {
        $directoryName = date('Y/m/d/H/i/');
        $dir = $this->getDir() . $directoryName;

        if (!$this->filesystem->exists($dir)) {
            $this->filesystem->mkdir($dir, 0755);
        }

        return $directoryName;
    }

    public function lock(): void
    {
        $this->filesystem->touch($this->getDir() . self::LOCK_FILE_NAME);
    }

    public function unlock(): void
    {
        $dir = $this->getDir();

        if ($this->filesystem->exists($dir . self::LOCK_FILE_NAME)) {
            $this->filesystem->remove($dir . self::LOCK_FILE_NAME);
        }
    }

    public function isLocked(): bool
    {
        return $this->filesystem->exists($this->getDir() . self::LOCK_FILE_NAME);
    }
}

class_alias(FileManager::class, 'EzSystems\EzRecommendationClient\File\FileManager');
