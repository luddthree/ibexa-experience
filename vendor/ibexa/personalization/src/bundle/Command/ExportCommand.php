<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Personalization\Command;

use Exception;
use Ibexa\Bundle\Core\Command\BackwardCompatibleCommand;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Personalization\Exception\InvalidInstallationKeyException;
use Ibexa\Personalization\Export\Input\CommandInputResolverInterface;
use Ibexa\Personalization\Exporter\ExporterInterface;
use Ibexa\Personalization\Factory\Export\ParametersFactoryInterface;
use Ibexa\Personalization\Service\Setting\SettingServiceInterface;
use Ibexa\Personalization\Value\Export\Parameters;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Generates and export content to Recommendation Server for a given command options.
 */
final class ExportCommand extends Command implements BackwardCompatibleCommand
{
    protected static $defaultName = 'ibexa:personalization:run-export';

    private const DEPRECATED_ALIASES = ['ibexa:recommendation:run-export'];
    private const DEFAULT_REPOSITORY_USER = 'anonymous';

    private ExporterInterface $exporter;

    private LoggerInterface $logger;

    private CommandInputResolverInterface $inputResolver;

    private ParametersFactoryInterface $exportParametersFactory;

    private UserService $userService;

    private PermissionResolver $permissionResolver;

    private SettingServiceInterface $settingService;

    public function __construct(
        ExporterInterface $exporter,
        LoggerInterface $logger,
        CommandInputResolverInterface $inputResolver,
        ParametersFactoryInterface $exportParametersFactory,
        UserService $userService,
        PermissionResolver $permissionResolver,
        SettingServiceInterface $settingService
    ) {
        parent::__construct();

        $this->exporter = $exporter;
        $this->logger = $logger;
        $this->inputResolver = $inputResolver;
        $this->exportParametersFactory = $exportParametersFactory;
        $this->userService = $userService;
        $this->permissionResolver = $permissionResolver;
        $this->settingService = $settingService;
    }

    protected function configure(): void
    {
        $this
            ->setAliases(self::DEPRECATED_ALIASES)
            ->setDescription('Run export to files.')
            ->addOption('customer-id', null, InputOption::VALUE_REQUIRED, 'Personalization customer id')
            ->addOption('license-key', null, InputOption::VALUE_REQUIRED, 'Personalization license key')
            ->addOption('web-hook', null, InputOption::VALUE_OPTIONAL, 'Recommendation engine URI used to send recommendation data')
            ->addOption('host', null, InputOption::VALUE_OPTIONAL, 'Host used to export files to be downloaded from')
            ->addOption('item-type-identifier-list', null, InputOption::VALUE_REQUIRED, 'List of item types identifiers')
            ->addOption('languages', null, InputOption::VALUE_REQUIRED, 'List of items languages')
            ->addOption('page-size', null, InputOption::VALUE_OPTIONAL, '', '500')
            ->addOption('user', 'u', InputOption::VALUE_REQUIRED, 'Ibexa username', self::DEFAULT_REPOSITORY_USER);
    }

    /**
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->isInstallationKeyFound();

        $this->permissionResolver->setCurrentUserReference(
            $this->userService->loadUserByLogin($input->getOption('user'))
        );

        try {
            $io = new SymfonyStyle($input, $output);
            date_default_timezone_set('UTC');
            $io->title('Recommendation export');
            $parameters = $this->createExportParameters($input, $this->getApplication());

            if (!$this->exporter->hasExportItems($parameters)) {
                $io->error('No export items found');

                return self::FAILURE;
            }

            $io->text('Starting export process...');
            $this->exporter->export($parameters);
            $io->success('Export completed successfully');

            return self::SUCCESS;
        } catch (Exception $e) {
            $this->logger->error('Error during export process: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * @return array<string>
     */
    public function getDeprecatedAliases(): array
    {
        return self::DEPRECATED_ALIASES;
    }

    /**
     * @throws \Ibexa\Personalization\Exception\InvalidInstallationKeyException
     */
    private function isInstallationKeyFound(): void
    {
        try {
            $installationKey = $this->settingService->getInstallationKey();
        } catch (NotFoundException $exception) {
            throw new InvalidInstallationKeyException('Missing installation key');
        }

        if (empty($installationKey)) {
            throw new InvalidInstallationKeyException('Missing installation key');
        }

        if (!$this->settingService->getAcceptanceStatus($installationKey)->isAccepted()) {
            throw new InvalidInstallationKeyException('Installation key has not been accepted yet');
        }
    }

    /**
     * @throws \Ibexa\Personalization\Exception\MissingExportParameterException
     */
    private function createExportParameters(InputInterface $input, ?Application $application): Parameters
    {
        return $this->exportParametersFactory->create(
            $this->inputResolver->resolve($input, $application),
            ParametersFactoryInterface::COMMAND_TYPE
        );
    }
}
