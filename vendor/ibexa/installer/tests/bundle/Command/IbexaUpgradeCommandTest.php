<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Installer\Command;

use Ibexa\Bundle\Commerce\Eshop\Api\Configuration\CommerceSiteConfig;
use Ibexa\Bundle\Installer\Command\IbexaUpgradeCommand;
use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Core\Repository\RoleService;
use Ibexa\Contracts\Migration\MigrationService;
use Ibexa\Installer\Gateway\SiteDataGateway;
use Ibexa\Installer\Provisioner\CommerceProvisioner;
use Ibexa\Tests\Bundle\Core\EventListener\Stubs\TestOutput;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * @internal
 */
final class IbexaUpgradeCommandTest extends TestCase
{
    /** @var \Ibexa\Contracts\Migration\MigrationService */
    private $migrationService;

    /**
     * @throws \Exception
     */
    public function testExecute(): void
    {
        $this->migrationService = $this->createMock(MigrationService::class);
        $command = $this->prepareCommand();

        $input = new ArrayInput([]);
        $input->setInteractive(false);
        self::assertSame(0, $command->run($input, new TestOutput()));
    }

    public function testSkipsAlreadyExecutedMigrations(): void
    {
        $this->migrationService = $this->createMock(MigrationService::class);

        $this->migrationService
            ->method('isMigrationExecuted')
            ->willReturn(true);

        $this->migrationService
            ->expects(self::never())
            ->method('executeOne');

        $command = $this->prepareCommand();

        $input = new ArrayInput([]);
        $input->setInteractive(false);
        self::assertSame(0, $command->run($input, new TestOutput()));
    }

    private function prepareCommand(): IbexaUpgradeCommand
    {
        $gateway = $this->createMock(SiteDataGateway::class);
        $kernel = $this->createMock(KernelInterface::class);

        $kernel
            ->method('locateResource')
            ->withConsecutive(
                ['@IbexaInstallerBundle/Resources/install/migrations/roles/roles_pb_update.yaml'],
                ['@IbexaInstallerBundle/Resources/install/migrations/roles/commerce_roles_pb_update.yaml']
            )
            ->willReturnOnConsecutiveCalls(
                __DIR__ . '/../../../src/bundle/Resources/install/legacy_commerce/roles/roles_pb_update.yaml',
                __DIR__ . '/../../../src/bundle/Resources/install/legacy_commerce/roles/commerce_roles_pb_update.yaml'
            );

        $commerceSiteConfig = $this->createMock(CommerceSiteConfig::class);
        $commerceSiteConfig->method('isCommerceEnabled')->willReturn(true);

        $gateway->method('getLegacySchemaVersion')->willReturn('3.3.0');
        $gateway->method('getDXPSchemaVersion')->willReturn(null);
        $kernel->method('locateResource')->willReturn('');

        return new IbexaUpgradeCommand(
            $gateway,
            $this->createMock(CommerceProvisioner::class),
            $this->migrationService,
            $kernel,
            $commerceSiteConfig,
            $this->createMock(ContentTypeService::class),
            $this->createMock(Repository::class),
            $this->createMock(RoleService::class)
        );
    }
}

class_alias(IbexaUpgradeCommandTest::class, 'Ibexa\Platform\Tests\Bundle\Installer\Command\IbexaUpgradeCommandTest');
