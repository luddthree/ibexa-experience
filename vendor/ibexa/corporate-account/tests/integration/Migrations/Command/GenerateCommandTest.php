<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\CorporateAccount\Migrations\Command;

use Ibexa\Tests\Integration\CorporateAccount\IbexaKernelTestCase;
use League\Flysystem\FilesystemOperator;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @covers \Ibexa\Bundle\Migration\Command\GenerateCommand
 */
final class GenerateCommandTest extends IbexaKernelTestCase
{
    private Command $command;

    private CommandTester $commandTester;

    private FilesystemOperator $flysystem;

    protected function setUp(): void
    {
        parent::setUp();

        $application = new Application(self::$kernel);
        $application->setAutoExit(false);
        $this->command = $application->find('ibexa:migrations:generate');
        $this->commandTester = new CommandTester($this->command);
        $this->flysystem = self::getFilesystem();

        self::setAdministratorUser();
    }

    /**
     * @dataProvider provideInputs
     *
     * @param array<string, scalar|scalar[]> $input
     *
     * @throws \League\Flysystem\FilesystemException
     * @throws \Exception
     */
    public function testBaseExecution(array $input, string $expectedFile): void
    {
        if (empty(self::getCompanyService()->getCompanies())) {
            self::fail(
                'This test requires test bootstrap to execute '
                . './tests/integration/_migrations/company_setup.yaml'
            );
        }

        self::assertCount(0, $this->flysystem->listContents('.'));
        $this->commandTester->execute($input);

        $output = $this->commandTester->getDisplay();
        self::assertStringContainsString('Done!', $output);
        $filesystemContents = iterator_to_array($this->flysystem->listContents('migrations/'));
        self::assertCount(1, $filesystemContents);
        $file = $filesystemContents[0];
        $generatedContents = $this->flysystem->read($file['path']);
        self::assertIsString($generatedContents);
        self::assertOutputEqualsFile($expectedFile, $generatedContents);
    }

    /**
     * @return iterable<string, array{array<string, scalar|scalar[]>, string, 2?: bool}>
     */
    public function provideInputs(): iterable
    {
        yield from $this->provideCompanyCommandData();
    }

    private static function assertOutputEqualsFile(string $expectedFile, string $generatedContents): void
    {
        self::assertStringEqualsFile(
            $expectedFile,
            $generatedContents,
            "Failed asserting snapshot file \"file://$expectedFile\" matches output"
        );
    }

    /**
     * @return iterable<string, array{array<string, scalar|scalar[]>, string}>
     */
    private function provideCompanyCommandData(): iterable
    {
        yield 'Company create' => [
            [
                '--type' => 'company',
                '--mode' => 'create',
                '--value' => ['*'],
            ],
            __DIR__ . '/generate-command-results/company-create.yaml',
        ];
    }
}
