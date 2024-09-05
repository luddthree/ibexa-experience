<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Migration\Bridge\KaliopMigration\Serializer\Reference;

use Ibexa\Bundle\Migration\Bridge\KaliopMigration\Serializer\Reference\ReferenceFileConverter;
use League\Flysystem\FilesystemOperator;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\Bundle\Migration\Bridge\KaliopMigration\Serializer\Reference\ReferenceFileConverter
 */
class ReferenceFileConverterTest extends TestCase
{
    private const SUBDIR = '__SUBDIR__';

    /** @var \League\Flysystem\FilesystemOperator|\PHPUnit\Framework\MockObject\MockObject * */
    private $filesystem;

    /** @var \Ibexa\Bundle\Migration\Bridge\KaliopMigration\Serializer\Reference\ReferenceFileConverter * */
    private $converter;

    protected function setUp(): void
    {
        $this->filesystem = $this->createMock(FilesystemOperator::class);

        $this->converter = new ReferenceFileConverter($this->filesystem, self::SUBDIR);
    }

    public function testConvertWhenFileDoesNotExist(): void
    {
        $this->filesystem
            ->method('fileExists')
            ->willReturn(false);

        $file = '__FILE__.yaml';
        $result = $this->converter->convert($file, '__OUTPUT__');

        self::assertEquals($file, $result);
    }

    public function testConvertWhenFileExistsAndIsNotConverted(): void
    {
        $file = '__FILE_WITH_REFERENCES__.yaml';
        $this->filesystem
            ->method('fileExists')
            ->withConsecutive(
                [$file],
                ['./__OUTPUT__/__SUBDIR__/__FILE_WITH_REFERENCES__.yaml'],
            )
            ->willReturnOnConsecutiveCalls(
                true,
                false
            );

        $this->filesystem
            ->method('read')
            ->with($file)
            ->willReturn($this->getReferencesInKaliopFormat());

        $this->filesystem
            ->expects($this->once())
            ->method('write')
            ->with('./__OUTPUT__/__SUBDIR__/__FILE_WITH_REFERENCES__.yaml', $this->getConvertedReferences())
            ->willReturnArgument(1);

        $result = $this->converter->convert($file, './__OUTPUT__/__FILE_WITH_STEP__.yaml');

        self::assertEquals('__SUBDIR__/__FILE_WITH_REFERENCES__.yaml', $result);
    }

    public function testConvertWhenFileExistsAndIsAlreadyConverted(): void
    {
        $file = '__FILE_WITH_REFERENCES__.yaml';
        $this->filesystem
            ->method('fileExists')
            ->withConsecutive(
                [$file],
                ['./__OUTPUT__/__SUBDIR__/__FILE_WITH_REFERENCES__.yaml'],
            )
            ->willReturnOnConsecutiveCalls(
                true,
                true
            );

        $this->filesystem
            ->method('read')
            ->with($file)
            ->willReturn($this->getReferencesInKaliopFormat());

        $this->filesystem
            ->expects($this->never())
            ->method('write');

        $result = $this->converter->convert($file, './__OUTPUT__/__FILE_WITH_STEP__.yaml');

        self::assertEquals('__SUBDIR__/__FILE_WITH_REFERENCES__.yaml', $result);
    }

    private function getReferencesInKaliopFormat(): string
    {
        return <<<YAML
            key: value
            key2: value2
            YAML;
    }

    private function getConvertedReferences(): string
    {
        return <<<YAML
                -
                    name: key
                    value: value
                -
                    name: key2
                    value: value2
                
                YAML;
    }
}

class_alias(ReferenceFileConverterTest::class, 'Ibexa\Platform\Tests\Bundle\Migration\Bridge\KaliopMigration\Serializer\Reference\ReferenceFileConverterTest');
