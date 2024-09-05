<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Personalization\File;

use Ibexa\Personalization\Exception\FileNotFoundException;
use Ibexa\Personalization\File\FileManager;
use Ibexa\Personalization\File\FileManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

final class FileManagerTest extends TestCase
{
    private FileManagerInterface $fileManager;

    /** @var \Symfony\Component\Filesystem\Filesystem|mixed|\PHPUnit\Framework\MockObject\MockObject */
    private Filesystem $baseFileSystem;

    private string $fixturesDocumentRoot;

    public function setUp(): void
    {
        parent::setUp();

        $this->baseFileSystem = $this->createMock(Filesystem::class);
        $this->fixturesDocumentRoot = __DIR__ . '/../fixtures/';
        $this->fileManager = new FileManager(
            $this->baseFileSystem,
            $this->fixturesDocumentRoot
        );
    }

    public function testLoad(): void
    {
        $this->baseFileSystem
            ->expects(self::once())
            ->method('exists')
            ->with($this->fixturesDocumentRoot . 'testfile.txt')
            ->willReturn(true);

        $content = $this->fileManager->load('testfile.txt');

        if (null !== $content) {
            self::assertStringEqualsFile(
                $this->fixturesDocumentRoot . 'testfile.txt',
                $content
            );
        }
    }

    public function testLoadNonexistentFile(): void
    {
        $this->baseFileSystem
            ->expects(self::once())
            ->method('exists')
            ->willReturn(false);

        $this->expectException(FileNotFoundException::class);
        $this->expectExceptionMessage('File: unexisting_file.txt not found.');
        $this->fileManager->load('unexisting_file.txt');
    }

    public function testSave(): void
    {
        $this->baseFileSystem
            ->expects(self::once())
            ->method('dumpFile')
            ->willReturn(true);

        $this->fileManager->save('testfile.txt', 'test');
    }

    public function testGetDir(): void
    {
        $result = $this->fileManager->getDir();

        $this->assertEquals($this->fixturesDocumentRoot, $result);
    }

    public function testCreateChunkDir(): void
    {
        $this->baseFileSystem
            ->expects(self::once())
            ->method('exists')
            ->willReturn(true);

        $result = $this->fileManager->createChunkDir();

        $this->assertTrue(\strlen($result) > 5);
    }

    public function testCreateChunkDirWithNonexistentDir(): void
    {
        $this->baseFileSystem
            ->expects(self::once())
            ->method('exists')
            ->willReturn(false);

        $this->baseFileSystem
            ->expects(self::once())
            ->method('mkdir')
            ->willReturn(true);

        $result = $this->fileManager->createChunkDir();

        $this->assertTrue(\strlen($result) > 5);
    }

    public function testLock(): void
    {
        $this->baseFileSystem
            ->expects(self::once())
            ->method('touch')
            ->willReturn(true);

        $this->fileManager->lock();
    }

    public function testUnlock(): void
    {
        $this->baseFileSystem
            ->expects(self::once())
            ->method('exists')
            ->willReturn(true);

        $this->baseFileSystem
            ->expects(self::once())
            ->method('remove')
            ->willReturn(true);

        $this->fileManager->unlock();
    }

    public function testUnlockWithoutLockedFile(): void
    {
        $this->baseFileSystem
            ->expects(self::once())
            ->method('exists')
            ->willReturn(false);

        $this->fileManager->unlock();
    }

    public function testIsLockedWithLockedFile(): void
    {
        $this->baseFileSystem
            ->expects(self::once())
            ->method('exists')
            ->willReturn(true);

        $this->assertTrue($this->fileManager->isLocked());
    }

    public function testIsLockedWithoutLockedFile(): void
    {
        $this->baseFileSystem
            ->expects(self::once())
            ->method('exists')
            ->willReturn(false);

        $this->assertFalse($this->fileManager->isLocked());
    }
}

class_alias(FileManagerTest::class, 'EzSystems\EzRecommendationClient\Tests\File\FileManagerTest');
