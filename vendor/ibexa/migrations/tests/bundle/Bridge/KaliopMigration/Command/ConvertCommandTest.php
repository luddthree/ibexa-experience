<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Migration\Bridge\KaliopMigration\Command;

use function file_exists;
use Ibexa\Core\Repository\Values\User\UserReference;
use Ibexa\Tests\Bundle\Migration\Command\AbstractCommandTest;
use Ibexa\Tests\Migration\FileLoadTrait;

/**
 * @covers \Ibexa\Bundle\Migration\Bridge\KaliopMigration\Command\ConvertCommand
 */
final class ConvertCommandTest extends AbstractCommandTest
{
    use FileLoadTrait;

    private const FIXTURE_ADMIN_ID = 14;

    protected static function getCommandName(): string
    {
        return 'ibexa:migrations:kaliop:convert';
    }

    protected function setUp(): void
    {
        parent::setUp();
        self::getPermissionResolver()->setCurrentUserReference(new UserReference(self::FIXTURE_ADMIN_ID));
    }

    /**
     * @dataProvider provideConversionFixtures
     */
    public function testConversionOnFiles(string $inputFile, string $outputFile): void
    {
        $input = __DIR__ . '/' . $inputFile;
        $output = __DIR__ . '/' . $outputFile;
        $contents = self::loadFile($input);
        if (!file_exists($output)) {
            touch($output);
        }

        $filesystem = self::getFilesystem();
        $inputFilename = 'foo.yaml';
        $outputFilename = 'bar.yaml';

        $filesystem->write($inputFilename, $contents);

        $commandInput = [
            '--input' => $inputFilename,
            '--output' => $outputFilename,
        ];

        $result = $this->commandTester->execute($commandInput);

        self::assertSame(0, $result);
        self::assertSame('', $this->commandTester->getDisplay());

        self::assertTrue($filesystem->fileExists($outputFilename), "Command should generate file $outputFile");
        $outputContent = $filesystem->read($outputFilename);

        self::assertIsString($outputContent, "Command should generate readable file $outputFile");
        self::assertStringEqualsFile($output, $outputContent, "Output file mismatch: $inputFile");
    }

    /**
     * @return iterable<array{string, string}>
     */
    public function provideConversionFixtures(): iterable
    {
        $fixtures = [
            ['fixtures/100.content-create.%s.yaml'],
            ['fixtures/200.location-update.%s.yaml'],
            ['fixtures/101.user-create.%s.yaml'],
            ['fixtures/101.user-update.%s.yaml'],
            ['fixtures/102.user-group-create.%s.yaml'],
            ['fixtures/201903150001.user-group-delete.%s.yaml'],
            ['fixtures/201903150000.object-state-create.%s.yaml'],
            ['fixtures/201903150001.content-delete-multiple.%s.yaml'],
            ['fixtures/201903150001.role-delete.%s.yaml'],
            ['fixtures/201903150002.section-create.%s.yaml'],
            ['fixtures/201903150003.language-create.%s.yaml'],
            ['fixtures/201903150004.content-update-rename.%s.yaml'],
            ['fixtures/201903150005.sql-execute.%s.yaml'],
            ['fixtures/201903150006.content-type-create.%s.yaml'],
            ['fixtures/201903150008.content-create-update.%s.yaml'],
            ['fixtures/201906061336.content-type-update.%s.yaml'],
            ['fixtures/201906061720.role-create.%s.yaml'],
            ['fixtures/201907121730.section-update.%s.yaml'],
            ['fixtures/201909021615.content-delete.%s.yaml'],
            ['fixtures/201909021645.role-update.%s.yaml'],
            ['fixtures/202001160900.service-call.%s.yaml'],
        ];

        foreach ($fixtures as $fileData) {
            $file = $fileData[0];
            $inputFile = sprintf($file, 'input');
            $outputFile = sprintf($file, 'output');

            yield $inputFile => [
                $inputFile,
                $outputFile,
            ];
        }
    }
}

class_alias(ConvertCommandTest::class, 'Ibexa\Platform\Tests\Bundle\Migration\Bridge\KaliopMigration\Command\ConvertCommandTest');
