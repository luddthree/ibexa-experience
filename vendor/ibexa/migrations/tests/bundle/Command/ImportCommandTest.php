<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Migration\Command;

use League\Flysystem\FilesystemOperator;
use org\bovigo\vfs\vfsStream;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Exception\RuntimeException;

/**
 * @covers \Ibexa\Bundle\Migration\Command\ImportCommand
 */
final class ImportCommandTest extends AbstractCommandTest
{
    /** @var \Ibexa\Bundle\Migration\Command\ImportCommand */
    protected $command;

    /** @var \org\bovigo\vfs\vfsStreamDirectory */
    private $root;

    private FilesystemOperator $migrationFilesystem;

    protected static function getCommandName(): string
    {
        return 'ibexa:migrations:import';
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->root = vfsStream::setup();
        $this->migrationFilesystem = self::getFilesystem();
    }

    public function testImportFileWithoutArgument(): void
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage(
            'Missing "file" argument. Either provide a file argument, or use --from-stdin option and '
            . 'supply migration contents from standard input.',
        );

        $this->commandTester->execute([]);
    }

    public function testImportFilesOnNonExistentFile(): void
    {
        self::assertFalse($this->migrationFilesystem->fileExists('foo.yaml'));
        self::assertFalse(file_exists($this->root->url() . '/foo.yaml'));

        self::expectException(RuntimeException::class);
        self::expectExceptionMessage(sprintf(
            '"%s" is not a file.',
            $this->root->url() . '/foo.yaml',
        ));

        $this->commandTester->execute([
            'file' => $this->root->url() . '/foo.yaml',
        ]);
    }

    public function testImportsFilesUsingFileName(): void
    {
        self::assertFalse($this->migrationFilesystem->fileExists('foo.yaml'));

        file_put_contents($this->root->url() . '/foo.yaml', '');
        $this->commandTester->execute([
            'file' => $this->root->url() . '/foo.yaml',
        ]);

        self::assertTrue($this->migrationFilesystem->fileExists('migrations/foo.yaml'));
    }

    public function testImportFilesUsingCustomName(): void
    {
        self::assertFalse($this->migrationFilesystem->fileExists('test'));

        file_put_contents($this->root->url() . '/foo.yaml', '');
        $this->commandTester->execute([
            'file' => $this->root->url() . '/foo.yaml',
            '--name' => 'test',
        ]);

        self::assertTrue($this->migrationFilesystem->fileExists('migrations/test'));
    }

    public function testThrowsExceptionAboutMissingNameOptionWhenStdinIsUsed(): void
    {
        $stdin = vfsStream::setup();
        file_put_contents($stdin->url() . '/test', '');

        $this->command->setStdin($stdin->url() . '/test');
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('"--name" option is required when using standard input as source');
        $this->commandTester->execute([
            '--from-stdin' => true,
        ]);
    }

    public function testImportFilesUsingStdin(): void
    {
        $stdin = vfsStream::setup();
        file_put_contents($stdin->url() . '/test', '__content__');

        $this->command->setStdin($stdin->url() . '/test');
        $this->commandTester->execute([
            '--from-stdin' => true,
            '--name' => 'test',
        ]);

        self::assertTrue($this->migrationFilesystem->fileExists('migrations/test'));
        self::assertSame('__content__', $this->migrationFilesystem->read('migrations/test'));
    }
}

class_alias(ImportCommandTest::class, 'Ibexa\Platform\Tests\Bundle\Migration\Command\ImportCommandTest');
