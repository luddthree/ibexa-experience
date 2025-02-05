<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\File;

interface FileManagerInterface
{
    /**
     * Loads file.
     *
     * @return string
     */
    public function load(string $file): ?string;

    /**
     * Saves the content to file.
     */
    public function save(string $file, string $content): void;

    /**
     * Returns directory.
     */
    public function getDir(): string;

    /**
     * Generates directory.
     */
    public function createChunkDir(): string;

    /**
     * Locks directory by creating lock file.
     */
    public function lock(): void;

    /**
     * Unlock directory by deleting lock file.
     */
    public function unlock(): void;

    /**
     * Checks if directory is locked.
     */
    public function isLocked(): bool;
}

class_alias(FileManagerInterface::class, 'EzSystems\EzRecommendationClient\File\FileManagerInterface');
