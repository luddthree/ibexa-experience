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
use League\Flysystem\FilesystemOperator;
use League\Flysystem\StorageAttributes;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @covers \Ibexa\Bundle\Migration\Bridge\KaliopMigration\Command\BulkConvertCommand
 */
final class BulkConvertCommandTest extends AbstractCommandTest
{
    use FileLoadTrait;

    private const FIXTURE_ADMIN_ID = 14;

    protected static function getCommandName(): string
    {
        return 'ibexa:migrations:kaliop:bulk-convert';
    }

    protected function setUp(): void
    {
        parent::setUp();
        self::getPermissionResolver()->setCurrentUserReference(new UserReference(self::FIXTURE_ADMIN_ID));
    }

    protected static function getKaliopMigrationFilesystem(): FilesystemOperator
    {
        return self::getServiceByClassName(
            FilesystemOperator::class,
            'ibexa.migrations.kaliop.io.flysystem.default_filesystem'
        );
    }

    /**
     * @dataProvider provideConversionFixtures
     *
     * @phpstan-param array{
     *     array{
     *         localInputFile: string,
     *         flysystemInputFile: string,
     *         localOutputFile: string,
     *         flysystemOutputFile: string,
     *     }
     * } $files
     */
    public function testConversionOnFiles(
        array $files,
        string $inputDir,
        ?string $outputDir,
        string $filesExpectedOutput,
        bool $recursive
    ): void {
        $filesystem = self::getKaliopMigrationFilesystem();

        $this->clearFilesystem($filesystem);

        foreach ($files as $file) {
            $inputFile = $file['localInputFile'];
            $outputFile = $file['localOutputFile'];
            $flysystemInputFile = $file['flysystemInputFile'];

            $contents = self::loadFile(__DIR__ . '/' . $inputFile);
            if (!file_exists(__DIR__ . '/' . $outputFile)) {
                touch(__DIR__ . '/' . $outputFile);
            }

            $filesystem->write($flysystemInputFile, $contents);
        }

        $commandInput = [
            'input-directory' => $inputDir,
            'output-directory' => $outputDir,
            '-R' => $recursive,
            '--continue-on-error' => true,
            '--discard-invalid-steps' => true,
            '--default-language' => 'eng-GB',
        ];

        $result = $this->commandTester->execute($commandInput);

        self::assertStringContainsString($filesExpectedOutput, $this->commandTester->getDisplay());
        self::assertSame(0, $result);

        foreach ($files as $file) {
            $outputFile = $file['flysystemOutputFile'];

            self::assertTrue($filesystem->fileExists($outputFile), sprintf(
                "Command should generate file: \"%s\". Available files:\n\"%s\"",
                $outputFile,
                implode("\"\n\"", $this->listFiles($filesystem))
            ));
            $outputContent = $filesystem->read($outputFile);

            self::assertIsString($outputContent, 'Command should generate readable file: ' . $outputFile);
            self::assertStringEqualsFile(
                __DIR__ . '/' . $file['localOutputFile'],
                $outputContent,
                sprintf('Expected output file "%s" does not match result', __DIR__ . '/' . $file['localOutputFile'])
            );
        }
    }

    public function testConversionFailureReportsFileCausingFailure(): void
    {
        $filesystem = self::getKaliopMigrationFilesystem();

        $this->clearFilesystem($filesystem);

        $filesystem->write('foo.yaml', '');

        $this->expectException('RuntimeException');
        $this->expectExceptionMessage('Converting file "foo.yaml" failed. Invalid migration data.');
        $this->commandTester->execute([
            'input-directory' => '',
        ]);
    }

    /**
     * @dataProvider provideTestFailureSamples
     */
    public function testConversionFailureReportsStepCausingFailureWhenThrowingException(
        string $content,
        string $expectedExceptionMessage
    ): void {
        $filesystem = self::getKaliopMigrationFilesystem();

        $this->clearFilesystem($filesystem);

        $filesystem->write('foo.yaml', $content);

        $this->expectException('RuntimeException');
        $this->expectExceptionMessage($expectedExceptionMessage);
        $this->commandTester->execute([
            'input-directory' => '',
        ]);
    }

    /**
     * @dataProvider provideTestFailureSamples
     */
    public function testConversionFailureReportsStepCausingFailureWhenErrorsAreAllowed(
        string $content
    ): void {
        $filesystem = self::getKaliopMigrationFilesystem();

        $this->clearFilesystem($filesystem);

        $filesystem->write('foo.yaml', $content);

        $this->commandTester->execute([
            'input-directory' => '',
            '--continue-on-error' => true,
        ]);

        self::assertSame(1, $this->commandTester->getStatusCode());
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public function provideTestFailureSamples(): iterable
    {
        yield 'empty array' => [
            '',
            'Converting file "foo.yaml" failed. Invalid migration data.',
        ];

        yield 'Single invalid step' => [
            <<<EXPECT
            -
                type: foo
                mode: bar
            EXPECT,
            'Converting file "foo.yaml" failed. Failed denormalizing Step 0. Unknown step. Unable to match Step denormalizer to type: foo, mode: bar.',
        ];

        yield 'Two invalid steps' => [
            <<<EXPECT
            -
                type: foo
                mode: bar
            -
                type: foo
                mode: bar
            EXPECT,
            'Converting file "foo.yaml" failed. Failed denormalizing Step 0. Unknown step. Unable to match Step denormalizer to type: foo, mode: bar.',
        ];
    }

    public function testConversionFailureReportsAllInvalidStepsWhenIgnoreInvalidStepsIsTrue(): void
    {
        $filesystem = self::getKaliopMigrationFilesystem();

        $this->clearFilesystem($filesystem);

        $content = <<<EXPECT
        -
            type: foo
            mode: bar
        -
            type: foo
            mode: bar
        EXPECT;
        $filesystem->write('foo.yaml', $content);

        $this->commandTester->execute([
            'input-directory' => '',
            '--discard-invalid-steps' => true,
        ]);

        self::assertSame('{  }', $filesystem->read('foo.yaml'));
        $this->assertStringContainsString(
            ' * [2] [InvalidArgumentException] Unknown step. Unable to match Step denormalizer to type: foo, mode: bar.',
            $this->commandTester->getDisplay(),
        );
        $this->assertSame(1, $this->commandTester->getStatusCode());
    }

    public function testConversionAllowsSkippingOverInvalidFiles(): void
    {
        $filesystem = self::getKaliopMigrationFilesystem();

        $this->clearFilesystem($filesystem);

        $content = <<<EXPECT
        -
            type: foo
            mode: bar
        -
            type: foo
            mode: bar
        EXPECT;
        $filesystem->write('foo.yaml', $content);

        $content = <<<EXPECT
        -
            type: foo
            mode: bar
        -
            type: foo
            mode: bar
        EXPECT;
        $filesystem->write('bar.yaml', $content);

        $this->commandTester->execute([
            'input-directory' => '',
            '--continue-on-error' => true,
        ]);

        $display = $this->commandTester->getDisplay();
        $this->assertStringContainsString(
            <<<EXPECT
             * ✘ bar.yaml
             * ✘ foo.yaml
            EXPECT,
            $display,
        );
        $this->assertStringContainsString(
            ' * [2] [InvalidArgumentException] Unknown step. Unable to match Step denormalizer to type: foo, mode: bar.',
            $display,
        );
    }

    public function testConversionFailureDoesNotStopExecutionWithContinueOnErrorOption(): void
    {
        $filesystem = self::getKaliopMigrationFilesystem();

        $this->clearFilesystem($filesystem);

        $filesystem->write('foo.yaml', '');
        $filesystem->write('bar.yaml', '[]');

        $result = $this->commandTester->execute([
            'input-directory' => '',
            '--continue-on-error' => true,
        ]);

        self::assertSame(1, $result);
        $output = $this->commandTester->getDisplay();
        self::assertStringContainsString(
            '[0] [Webmozart\Assert\InvalidArgumentException] Invalid migration data.',
            $output
        );
        self::assertStringContainsString(
            implode("\n", [
                'Processed 2 files',
                '✓ 1 OK',
                '✘ 1 FAILED',
            ]),
            $output,
        );
    }

    public function testConversionDisplaysFileNamesInVerboseMode(): void
    {
        $filesystem = self::getKaliopMigrationFilesystem();

        $this->clearFilesystem($filesystem);

        $filesystem->write('foo.yaml', '[]');
        $filesystem->write('bar.yaml', '[]');

        $result = $this->commandTester->execute([
            'input-directory' => '',
        ], [
            'verbosity' => OutputInterface::VERBOSITY_VERBOSE,
        ]);

        self::assertSame(0, $result);
        self::assertStringContainsString(
            implode("\n", [
                'Converting: "bar.yaml"',
                'Converting: "foo.yaml"',
                '',
            ]),
            $this->commandTester->getDisplay()
        );
    }

    /**
     * @return iterable<string, array{
     *     array<
     *         array{
     *             localInputFile: non-empty-string,
     *             flysystemInputFile: non-empty-string,
     *             localOutputFile: non-empty-string,
     *             flysystemOutputFile: non-empty-string,
     *         },
     *     >,
     *     string,
     *     string|null,
     *     string,
     *     bool,
     * }>
     */
    public function provideConversionFixtures(): iterable
    {
        $inputDir = '';
        $outputDir = '';
        $files = $this->generateFilesArray('', '', $outputDir);
        $commonExpectation = <<<EXPECT
             * ✓ 100.content-create.input.yaml
             * ✓ 101.user-create.input.yaml
             * ✓ 101.user-update.input.yaml
             * ✓ 102.user-group-create.input.yaml
             * ✓ 200.location-update.input.yaml
             * ✓ 201903150000.object-state-create.input.yaml
             * ✓ 201903150001.content-delete-multiple.input.yaml
             * ✓ 201903150001.role-delete.input.yaml
             * ✓ 201903150001.user-group-delete.input.yaml
             * ✓ 201903150003.language-create.input.yaml
             * ✓ 201903150004.content-update-rename.input.yaml
             * ✓ 201903150006.content-type-create-missing-main-lang.input.yaml
             * ✓ 201903150006.content-type-create.input.yaml
             * ✓ 201906061336.content-type-update.input.yaml
             * ✓ 201906061720.role-create.input.yaml
             * ✓ 201907121730.section-update.input.yaml
             * ✓ 201909021615.content-delete.input.yaml
             * ✓ 201909021645.role-update.input.yaml
             * ✓ 202001160900.service-call.input.yaml
            EXPECT;

        yield 'Root directory' => [
            $files,
            $inputDir,
            $outputDir,
            $commonExpectation,
            true,
        ];

        $files = $this->generateFilesArray('', '', '');

        yield 'Root directory with output dir not set' => [
            $files,
            $inputDir,
            null,
            $commonExpectation,
            true,
        ];

        $outputDir = '__output_dir__';
        $files = $this->generateFilesArray('aaa/bbb', '', $outputDir);

        yield '"__output_dir__" as output directory with internal directories' => [
            $files,
            $inputDir,
            $outputDir,
            <<<EXPECT
             * ✓ aaa/bbb/100.content-create.input.yaml
             * ✓ aaa/bbb/101.user-create.input.yaml
             * ✓ aaa/bbb/101.user-update.input.yaml
             * ✓ aaa/bbb/102.user-group-create.input.yaml
             * ✓ aaa/bbb/200.location-update.input.yaml
             * ✓ aaa/bbb/201903150000.object-state-create.input.yaml
             * ✓ aaa/bbb/201903150001.content-delete-multiple.input.yaml
             * ✓ aaa/bbb/201903150001.role-delete.input.yaml
             * ✓ aaa/bbb/201903150001.user-group-delete.input.yaml
             * ✓ aaa/bbb/201903150003.language-create.input.yaml
             * ✓ aaa/bbb/201903150004.content-update-rename.input.yaml
             * ✓ aaa/bbb/201903150006.content-type-create-missing-main-lang.input.yaml
             * ✓ aaa/bbb/201903150006.content-type-create.input.yaml
             * ✓ aaa/bbb/201906061336.content-type-update.input.yaml
             * ✓ aaa/bbb/201906061720.role-create.input.yaml
             * ✓ aaa/bbb/201907121730.section-update.input.yaml
             * ✓ aaa/bbb/201909021615.content-delete.input.yaml
             * ✓ aaa/bbb/201909021645.role-update.input.yaml
             * ✓ aaa/bbb/202001160900.service-call.input.yaml
            EXPECT,
            true,
        ];

        $outputDir = '__output_dir__';
        $files = $this->generateFilesArray('aaa/bbb', '__input_dir__', $outputDir);

        yield '"__output_dir__" as output directory, "__input_dir__" as input directory, with internal directories' => [
            $files,
            '__input_dir__',
            $outputDir,
            <<<EXPECT
             * ✓ __input_dir__/aaa/bbb/100.content-create.input.yaml
             * ✓ __input_dir__/aaa/bbb/101.user-create.input.yaml
             * ✓ __input_dir__/aaa/bbb/101.user-update.input.yaml
             * ✓ __input_dir__/aaa/bbb/102.user-group-create.input.yaml
             * ✓ __input_dir__/aaa/bbb/200.location-update.input.yaml
             * ✓ __input_dir__/aaa/bbb/201903150000.object-state-create.input.yaml
             * ✓ __input_dir__/aaa/bbb/201903150001.content-delete-multiple.input.yaml
             * ✓ __input_dir__/aaa/bbb/201903150001.role-delete.input.yaml
             * ✓ __input_dir__/aaa/bbb/201903150001.user-group-delete.input.yaml
             * ✓ __input_dir__/aaa/bbb/201903150003.language-create.input.yaml
             * ✓ __input_dir__/aaa/bbb/201903150004.content-update-rename.input.yaml
             * ✓ __input_dir__/aaa/bbb/201903150006.content-type-create-missing-main-lang.input.yaml
             * ✓ __input_dir__/aaa/bbb/201903150006.content-type-create.input.yaml
             * ✓ __input_dir__/aaa/bbb/201906061336.content-type-update.input.yaml
             * ✓ __input_dir__/aaa/bbb/201906061720.role-create.input.yaml
             * ✓ __input_dir__/aaa/bbb/201907121730.section-update.input.yaml
             * ✓ __input_dir__/aaa/bbb/201909021615.content-delete.input.yaml
             * ✓ __input_dir__/aaa/bbb/201909021645.role-update.input.yaml
             * ✓ __input_dir__/aaa/bbb/202001160900.service-call.input.yaml
            EXPECT,
            true,
        ];

        $outputDir = '__output_dir__';
        $files = $this->generateFilesArray('', '', $outputDir);

        yield '"__output_dir__" as output directory without internal directories' => [
            $files,
            $inputDir,
            $outputDir,
            $commonExpectation,
            false,
        ];

        $files = $this->generateEcommerceFilesArray();
        yield 'ecommerce master test' => [
            $files,
            '',
            'output',
            <<<EXPECT
             * ✓ ecommerce_migration_fixtures/content/Articles/202007121300_articles.yml
             * ✓ ecommerce_migration_fixtures/content/Catalog/201804161300_product_catalog.yml
             * ✓ ecommerce_migration_fixtures/content/Components/201804161300_components.yml
             * ✓ ecommerce_migration_fixtures/content_types/201804161300_content_types.yml
             * ✓ ecommerce_migration_fixtures/roles/201804161300_roles.yml
             * ✓ ecommerce_migration_fixtures/roles/201804161300_roles_pb_update.yml
             * ✓ ecommerce_migration_fixtures/sections/201804161300_sections.yml
             * ✓ ecommerce_migration_fixtures/users/201804161300_users.yml
            EXPECT,
            true,
        ];
    }

    /**
     * @return string[]
     *
     * @throws \League\Flysystem\FilesystemException
     */
    private function listFiles(FilesystemOperator $filesystem): array
    {
        return $filesystem
            ->listContents('', true)
            ->filter(static fn (StorageAttributes $attributes): bool => $attributes->isFile())
            ->map(static fn (StorageAttributes $attributes): string => $attributes->path())
            ->toArray();
    }

    /**
     * @param string $internalDirectories
     * @param string $outputDir
     *
     * @return array{
     *     array{
     *         localInputFile: non-empty-string,
     *         flysystemInputFile: non-empty-string,
     *         localOutputFile: non-empty-string,
     *         flysystemOutputFile: non-empty-string,
     *     },
     * }
     */
    private function generateFilesArray(string $internalDirectories, string $inputDir, string $outputDir): array
    {
        if ($internalDirectories !== '') {
            $internalDirectories = rtrim($internalDirectories, '/') . '/';
        }

        if ($inputDir !== '') {
            $inputDir = rtrim($inputDir, '/') . '/';
        }

        if ($outputDir !== '') {
            $outputDir = rtrim($outputDir, '/') . '/';
        }

        return [
            [
                'localInputFile' => 'fixtures/100.content-create.input.yaml',
                'flysystemInputFile' => "{$inputDir}{$internalDirectories}100.content-create.input.yaml",
                'localOutputFile' => 'fixtures/100.content-create.output.yaml',
                'flysystemOutputFile' => "{$outputDir}{$internalDirectories}100.content-create.input.yaml",
            ], [
                'localInputFile' => 'fixtures/200.location-update.input.yaml',
                'flysystemInputFile' => "{$inputDir}{$internalDirectories}200.location-update.input.yaml",
                'localOutputFile' => 'fixtures/200.location-update.output.yaml',
                'flysystemOutputFile' => "{$outputDir}{$internalDirectories}200.location-update.input.yaml",
            ], [
                'localInputFile' => 'fixtures/101.user-create.input.yaml',
                'flysystemInputFile' => "{$inputDir}{$internalDirectories}101.user-create.input.yaml",
                'localOutputFile' => 'fixtures/101.user-create.output.yaml',
                'flysystemOutputFile' => "{$outputDir}{$internalDirectories}101.user-create.input.yaml",
            ], [
                'localInputFile' => 'fixtures/101.user-update.input.yaml',
                'flysystemInputFile' => "{$inputDir}{$internalDirectories}101.user-update.input.yaml",
                'localOutputFile' => 'fixtures/101.user-update.output.yaml',
                'flysystemOutputFile' => "{$outputDir}{$internalDirectories}101.user-update.input.yaml",
            ], [
                'localInputFile' => 'fixtures/102.user-group-create.input.yaml',
                'flysystemInputFile' => "{$inputDir}{$internalDirectories}102.user-group-create.input.yaml",
                'localOutputFile' => 'fixtures/102.user-group-create.output.yaml',
                'flysystemOutputFile' => "{$outputDir}{$internalDirectories}102.user-group-create.input.yaml",
            ], [
                'localInputFile' => 'fixtures/201903150001.user-group-delete.input.yaml',
                'flysystemInputFile' => "{$inputDir}{$internalDirectories}201903150001.user-group-delete.input.yaml",
                'localOutputFile' => 'fixtures/201903150001.user-group-delete.output.yaml',
                'flysystemOutputFile' => "{$outputDir}{$internalDirectories}201903150001.user-group-delete.input.yaml",
            ], [
                'localInputFile' => 'fixtures/201903150000.object-state-create.input.yaml',
                'flysystemInputFile' => "{$inputDir}{$internalDirectories}201903150000.object-state-create.input.yaml",
                'localOutputFile' => 'fixtures/201903150000.object-state-create.output.yaml',
                'flysystemOutputFile' => "{$outputDir}{$internalDirectories}201903150000.object-state-create.input.yaml",
            ], [
                'localInputFile' => 'fixtures/201903150001.content-delete-multiple.input.yaml',
                'flysystemInputFile' => "{$inputDir}{$internalDirectories}201903150001.content-delete-multiple.input.yaml",
                'localOutputFile' => 'fixtures/201903150001.content-delete-multiple.output.yaml',
                'flysystemOutputFile' => "{$outputDir}{$internalDirectories}201903150001.content-delete-multiple.input.yaml",
            ], [
                'localInputFile' => 'fixtures/201903150001.role-delete.input.yaml',
                'flysystemInputFile' => "{$inputDir}{$internalDirectories}201903150001.role-delete.input.yaml",
                'localOutputFile' => 'fixtures/201903150001.role-delete.output.yaml',
                'flysystemOutputFile' => "{$outputDir}{$internalDirectories}201903150001.role-delete.input.yaml",
            ], [
                'localInputFile' => 'fixtures/201903150003.language-create.input.yaml',
                'flysystemInputFile' => "{$inputDir}{$internalDirectories}201903150003.language-create.input.yaml",
                'localOutputFile' => 'fixtures/201903150003.language-create.output.yaml',
                'flysystemOutputFile' => "{$outputDir}{$internalDirectories}201903150003.language-create.input.yaml",
            ], [
                'localInputFile' => 'fixtures/201903150004.content-update-rename.input.yaml',
                'flysystemInputFile' => "{$inputDir}{$internalDirectories}201903150004.content-update-rename.input.yaml",
                'localOutputFile' => 'fixtures/201903150004.content-update-rename.output.yaml',
                'flysystemOutputFile' => "{$outputDir}{$internalDirectories}201903150004.content-update-rename.input.yaml",
            ], [
                'localInputFile' => 'fixtures/201903150006.content-type-create-missing-main-lang.input.yaml',
                'flysystemInputFile' => "{$inputDir}{$internalDirectories}201903150006.content-type-create-missing-main-lang.input.yaml",
                'localOutputFile' => 'fixtures/201903150006.content-type-create-missing-main-lang.output.yaml',
                'flysystemOutputFile' => "{$outputDir}{$internalDirectories}201903150006.content-type-create-missing-main-lang.input.yaml",
            ], [
                'localInputFile' => 'fixtures/201903150006.content-type-create.input.yaml',
                'flysystemInputFile' => "{$inputDir}{$internalDirectories}201903150006.content-type-create.input.yaml",
                'localOutputFile' => 'fixtures/201903150006.content-type-create.output.yaml',
                'flysystemOutputFile' => "{$outputDir}{$internalDirectories}201903150006.content-type-create.input.yaml",
            ], [
                'localInputFile' => 'fixtures/201906061336.content-type-update.input.yaml',
                'flysystemInputFile' => "{$inputDir}{$internalDirectories}201906061336.content-type-update.input.yaml",
                'localOutputFile' => 'fixtures/201906061336.content-type-update.output.yaml',
                'flysystemOutputFile' => "{$outputDir}{$internalDirectories}201906061336.content-type-update.input.yaml",
            ], [
                'localInputFile' => 'fixtures/201906061720.role-create.input.yaml',
                'flysystemInputFile' => "{$inputDir}{$internalDirectories}201906061720.role-create.input.yaml",
                'localOutputFile' => 'fixtures/201906061720.role-create.output.yaml',
                'flysystemOutputFile' => "{$outputDir}{$internalDirectories}201906061720.role-create.input.yaml",
            ], [
                'localInputFile' => 'fixtures/201907121730.section-update.input.yaml',
                'flysystemInputFile' => "{$inputDir}{$internalDirectories}201907121730.section-update.input.yaml",
                'localOutputFile' => 'fixtures/201907121730.section-update.output.yaml',
                'flysystemOutputFile' => "{$outputDir}{$internalDirectories}201907121730.section-update.input.yaml",
            ], [
                'localInputFile' => 'fixtures/201909021615.content-delete.input.yaml',
                'flysystemInputFile' => "{$inputDir}{$internalDirectories}201909021615.content-delete.input.yaml",
                'localOutputFile' => 'fixtures/201909021615.content-delete.output.yaml',
                'flysystemOutputFile' => "{$outputDir}{$internalDirectories}201909021615.content-delete.input.yaml",
            ], [
                'localInputFile' => 'fixtures/201909021645.role-update.input.yaml',
                'flysystemInputFile' => "{$inputDir}{$internalDirectories}201909021645.role-update.input.yaml",
                'localOutputFile' => 'fixtures/201909021645.role-update.output.yaml',
                'flysystemOutputFile' => "{$outputDir}{$internalDirectories}201909021645.role-update.input.yaml",
            ], [
                'localInputFile' => 'fixtures/202001160900.service-call.input.yaml',
                'flysystemInputFile' => "{$inputDir}{$internalDirectories}202001160900.service-call.input.yaml",
                'localOutputFile' => 'fixtures/202001160900.service-call.output.yaml',
                'flysystemOutputFile' => "{$outputDir}{$internalDirectories}202001160900.service-call.input.yaml",
            ],
        ];
    }

    /**
     * @return array<
     *     array{
     *         localInputFile: non-empty-string,
     *         flysystemInputFile: non-empty-string,
     *         localOutputFile: non-empty-string,
     *         flysystemOutputFile: non-empty-string,
     *     },
     * >
     */
    private function generateEcommerceFilesArray(): array
    {
        $files = [
            'ecommerce_migration_fixtures/content/Articles/202007121300_articles.yml',
            'ecommerce_migration_fixtures/content/Catalog/201804161300_product_catalog.yml',
            'ecommerce_migration_fixtures/content/Components/201804161300_components.yml',
            'ecommerce_migration_fixtures/content_types/201804161300_content_types.yml',
            'ecommerce_migration_fixtures/roles/201804161300_roles.yml',
            'ecommerce_migration_fixtures/roles/201804161300_roles_pb_update.yml',
            'ecommerce_migration_fixtures/sections/201804161300_sections.yml',
            'ecommerce_migration_fixtures/users/201804161300_users.yml',
        ];

        $filesArray = [];
        foreach ($files as $filepath) {
            $filesArray[] = [
                'localInputFile' => $filepath,
                'flysystemInputFile' => $filepath,
                'localOutputFile' => $filepath . '.output',
                'flysystemOutputFile' => 'output/' . $filepath,
            ];
        }

        return $filesArray;
    }

    private function clearFilesystem(FilesystemOperator $filesystem): void
    {
        foreach ($filesystem->listContents('') as $storageAttribute) {
            if ($storageAttribute->isDir()) {
                $filesystem->deleteDirectory($storageAttribute->path());
            } else {
                $filesystem->delete($storageAttribute->path());
            }
        }
    }
}

class_alias(BulkConvertCommandTest::class, 'Ibexa\Platform\Tests\Bundle\Migration\Bridge\KaliopMigration\Command\BulkConvertCommandTest');
