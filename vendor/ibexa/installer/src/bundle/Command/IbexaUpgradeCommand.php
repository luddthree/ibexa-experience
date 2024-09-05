<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Installer\Command;

use Ibexa\Bundle\Commerce\Eshop\Api\Configuration\CommerceSiteConfig;
use Ibexa\Contracts\Core\Ibexa;
use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Core\Repository\RoleService;
use Ibexa\Contracts\Migration\MigrationService;
use Ibexa\Installer\Gateway\SiteDataGateway;
use Ibexa\Installer\Output\SkipMigrationMessage;
use Ibexa\Installer\Provisioner\CommerceProvisioner;
use Ibexa\Migration\Repository\Migration;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * @internal
 */
final class IbexaUpgradeCommand extends Command
{
    private const MIGRATION_LOCATION = '@IbexaInstallerBundle/Resources/install/migrations';
    private const ROLES_MIGRATION_FILE_PATH = 'roles/roles_pb_update.yaml';
    private const COMMERCE_ROLES_MIGRATION_FILE_PATH = 'roles/commerce_roles_pb_update.yaml';

    protected static $defaultName = 'ibexa:upgrade';

    /** @var \Ibexa\Installer\Gateway\SiteDataGateway */
    private $gateway;

    /** @var \Ibexa\Installer\Provisioner\CommerceProvisioner */
    private $commerceProvisioner;

    /** @var \Ibexa\Contracts\Migration\MigrationService */
    private $migrationService;

    /** @var \Symfony\Component\HttpKernel\KernelInterface */
    private $kernel;

    private ?CommerceSiteConfig $commerceSiteConfig;

    /** @var \Ibexa\Contracts\Core\Repository\ContentTypeService */
    private $contentTypeService;

    /** @var \Ibexa\Contracts\Core\Repository\RoleService */
    private $roleService;

    /** @var \Ibexa\Contracts\Core\Repository\Repository */
    private $repository;

    public function __construct(
        SiteDataGateway $gateway,
        CommerceProvisioner $commerceProvisioner,
        MigrationService $migrationService,
        KernelInterface $kernel,
        ?CommerceSiteConfig $commerceSiteConfig,
        ContentTypeService $contentTypeService,
        Repository $repository,
        RoleService $roleService
    ) {
        $this->gateway = $gateway;
        $this->commerceProvisioner = $commerceProvisioner;
        $this->migrationService = $migrationService;
        $this->kernel = $kernel;
        $this->commerceSiteConfig = $commerceSiteConfig;
        $this->contentTypeService = $contentTypeService;
        $this->repository = $repository;
        $this->roleService = $roleService;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption(
            'force',
            'f',
            InputOption::VALUE_NONE,
            'Force upgrade (even if already performed), as long as Repository is installed'
        );
        $this->setDescription('Upgrade the current instance to Ibexa DXP');
    }

    /**
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $this->validateRepositoryState((bool)$input->getOption('force'));

        if (!$io->confirm(
            'This installer will upgrade current instance to Ibexa DXP. ' .
            'Are you sure you want to continue?'
        )) {
            return self::FAILURE;
        }

        if ($this->isCommerceEnabled()) {
            $this->migrateToCommerce($io);
        }

        $this->gateway->setDXPSchemaVersion(Ibexa::VERSION);

        $io->success('Successfully upgraded existing instance to Ibexa DXP');

        return self::SUCCESS;
    }

    private function validateRepositoryState(bool $force): void
    {
        $coreSchemaVersion = $this->gateway->getLegacySchemaVersion();
        $dxpVersion = $this->gateway->getDXPSchemaVersion();
        if (empty($coreSchemaVersion) && empty($dxpVersion)) {
            throw new RuntimeException(
                'Looks like Ibexa Repository is not installed yet. Run ibexa:install command instead'
            );
        }

        if (!$force && !empty($dxpVersion)) {
            // note: in the future it will be possible to perform regular upgrades using this command
            throw new RuntimeException(
                "The current instance is already upgraded to Ibexa DXP (version $dxpVersion). " .
                'If you\'re upgrading your installation to the newer version, follow ' .
                'the standard upgrade procedure described in the official documentation ' .
                '(https://doc.ibexa.co)'
            );
        }
    }

    private function isCommerceEnabled(): bool
    {
        return $this->commerceSiteConfig !== null && $this->commerceSiteConfig->isCommerceEnabled();
    }

    private function migrateToCommerce(SymfonyStyle $io): void
    {
        $this->commerceProvisioner->provision($io);
        $this->migratePageBuilderRoles($io);
        $this->migrateCommercePageBuilderRoles($io);
    }

    private function contentTypeExists(string $identifier): bool
    {
        try {
            $this->contentTypeService->loadContentTypeByIdentifier($identifier);

            return true;
        } catch (NotFoundException $e) {
            return false;
        }
    }

    private function migratePageBuilderRoles(OutputInterface $output): void
    {
        // further step (roles update) requires landing_page content type to exist
        if (!$this->contentTypeExists('landing_page')) {
            return;
        }

        $file = $this->kernel->locateResource(
            self::MIGRATION_LOCATION . '/' . self::ROLES_MIGRATION_FILE_PATH
        );
        $migration = new Migration(self::ROLES_MIGRATION_FILE_PATH, file_get_contents($file));
        $this->migrationService->add($migration);
        if ($this->migrationService->isMigrationExecuted($migration)) {
            $output->writeln(SkipMigrationMessage::createMessage($migration));

            return;
        }
        $this->migrationService->executeOne($migration);
    }

    private function migrateCommercePageBuilderRoles(OutputInterface $output): void
    {
        if (!$this->contentTypeExists('landing_page') || !$this->roleExists('Ecommerce anonymous')) {
            return;
        }

        $file = $this->kernel->locateResource(
            self::MIGRATION_LOCATION . '/' . self::COMMERCE_ROLES_MIGRATION_FILE_PATH
        );
        $migration = new Migration(self::COMMERCE_ROLES_MIGRATION_FILE_PATH, file_get_contents($file));
        $this->migrationService->add($migration);
        if ($this->migrationService->isMigrationExecuted($migration)) {
            $output->writeln(SkipMigrationMessage::createMessage($migration));

            return;
        }
        $this->migrationService->executeOne($migration);
    }

    private function roleExists(string $roleIdentifier): bool
    {
        $roleService = $this->roleService;
        try {
            $this->repository->sudo(static function () use ($roleService, $roleIdentifier): void {
                $roleService->loadRoleByIdentifier($roleIdentifier);
            });

            return true;
        } catch (NotFoundException $e) {
            return false;
        }
    }
}

class_alias(IbexaUpgradeCommand::class, 'Ibexa\Platform\Bundle\Installer\Command\IbexaUpgradeCommand');
