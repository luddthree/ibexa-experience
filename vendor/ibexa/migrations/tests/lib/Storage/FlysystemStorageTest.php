<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Migration\Storage;

use Ibexa\Contracts\Migration\Exception\MigrationAlreadyExistsException;
use Ibexa\Migration\Repository\Migration;
use Ibexa\Migration\Storage\FlysystemMigrationStorage;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemOperator;
use League\Flysystem\InMemory\InMemoryFilesystemAdapter;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\Migration\Storage\FlysystemMigrationStorage
 */
final class FlysystemStorageTest extends TestCase
{
    public function testStore(): FilesystemOperator
    {
        $filesystem = new Filesystem(new InMemoryFilesystemAdapter());
        $storage = new FlysystemMigrationStorage($filesystem);

        self::assertCount(0, $filesystem->listContents('.'));
        $storage->store(new Migration('test', 'foo'));

        self::assertCount(1, $filesystem->listContents('.'));

        return $filesystem;
    }

    /**
     * @depends testStore
     */
    public function testStoreThrowsExceptionWhenMigrationWithSameNameIsAddedWithDifferentContent(
        FilesystemOperator $filesystem
    ): void {
        $storage = new FlysystemMigrationStorage($filesystem);
        self::assertTrue($filesystem->fileExists('migrations/test'));

        $this->expectException(MigrationAlreadyExistsException::class);
        $this->expectExceptionMessage('Migration "test" already exists.');
        $storage->store(new Migration('test', 'bar'));
    }

    /**
     * @depends testStore
     */
    public function testStoreThrowsExceptionWhenMigrationWithSameNameIsAddedWithSameContent(
        FilesystemOperator $filesystem
    ): void {
        $storage = new FlysystemMigrationStorage($filesystem);
        self::assertTrue($filesystem->fileExists('migrations/test'));

        $storage->store(new Migration('test', 'foo'));
    }
}

class_alias(FlysystemStorageTest::class, 'Ibexa\Platform\Tests\Migration\Storage\FlysystemStorageTest');
