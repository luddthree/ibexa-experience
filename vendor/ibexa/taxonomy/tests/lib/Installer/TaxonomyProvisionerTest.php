<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Taxonomy\Installer;

use Ibexa\Bundle\Core\ApiLoader\RepositoryConfigurationProvider;
use Ibexa\Contracts\Migration\Metadata\Storage\MetadataStorage;
use Ibexa\Contracts\Migration\MigrationService;
use Ibexa\Installer\Executor\CommandExecutor;
use Ibexa\Taxonomy\Installer\TaxonomyProvisioner;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\KernelInterface;

final class TaxonomyProvisionerTest extends TestCase
{
    private TaxonomyProvisioner $provisioner;

    /** @var \Ibexa\Installer\Executor\CommandExecutor|\PHPUnit\Framework\MockObject\MockObject */
    private CommandExecutor $commandExecutor;

    /** @var \Ibexa\Contracts\Migration\MigrationService|\PHPUnit\Framework\MockObject\MockObject */
    private MigrationService $migrationService;

    /** @var \Ibexa\Contracts\Migration\Metadata\Storage\MetadataStorage|\PHPUnit\Framework\MockObject\MockObject */
    private MetadataStorage $metadataStorage;

    /** @var \Symfony\Component\HttpKernel\KernelInterface|\PHPUnit\Framework\MockObject\MockObject */
    private KernelInterface $kernel;

    /** @var \Ibexa\Bundle\Core\ApiLoader\RepositoryConfigurationProvider|\PHPUnit\Framework\MockObject\MockObject */
    private RepositoryConfigurationProvider $repositoryConfigurationProvider;

    protected function setUp(): void
    {
        $this->commandExecutor = $this->createMock(CommandExecutor::class);
        $this->migrationService = $this->createMock(MigrationService::class);
        $this->metadataStorage = $this->createMock(MetadataStorage::class);
        $this->kernel = $this->mockKernel();
        $this->repositoryConfigurationProvider = $this->mockRepositoryConfigurationProvider();

        $this->provisioner = new TaxonomyProvisioner(
            $this->commandExecutor,
            $this->migrationService,
            $this->metadataStorage,
            $this->kernel,
            $this->repositoryConfigurationProvider,
        );
    }

    public function testExecutesSchemaUpdate(): void
    {
        $output = $this->createMock(OutputInterface::class);

        $this->commandExecutor
            ->expects(self::once())
            ->method('executeCommand')
            ->with(
                $output,
                'doctrine:schema:update --dump-sql --force --em=ibexa_foobar',
                0
            );

        $this->provisioner->provision($output);
    }

    public function testExecutesMigrations(): void
    {
        $output = $this->createMock(OutputInterface::class);

        $this->migrationService
            ->expects(self::atLeastOnce())
            ->method('executeOne');

        $this->provisioner->provision($output);
    }

    public function testSkipAlreadyExecutedMigrations(): void
    {
        $output = $this->createMock(OutputInterface::class);
        $output
            ->expects(self::atLeastOnce())
            ->method('writeln');

        $this->migrationService
            ->method('isMigrationExecuted')
            ->willReturn(true);

        $this->provisioner->provision($output);
    }

    /**
     * @return \Ibexa\Bundle\Core\ApiLoader\RepositoryConfigurationProvider|\PHPUnit\Framework\MockObject\MockObject
     */
    private function mockRepositoryConfigurationProvider(): RepositoryConfigurationProvider
    {
        $repositoryConfigurationProvider = $this->createMock(RepositoryConfigurationProvider::class);
        $repositoryConfigurationProvider
            ->method('getStorageConnectionName')
            ->willReturn('foobar');

        return $repositoryConfigurationProvider;
    }

    /**
     * @return \Symfony\Component\HttpKernel\KernelInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected function mockKernel(): KernelInterface
    {
        $stdin = vfsStream::setup();

        $kernel = $this->createMock(KernelInterface::class);
        $kernel
            ->method('locateResource')
            ->willReturn($stdin->url() . '/file.yaml');

        file_put_contents($stdin->url() . '/file.yaml', 'test: ~');

        return $kernel;
    }
}
