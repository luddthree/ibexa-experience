<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Installer\Event\Subscriber;

use Composer\InstalledVersions;
use Ibexa\Bundle\RepositoryInstaller\Command\InstallPlatformCommand;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class InstallCommandSubscriber implements EventSubscriberInterface
{
    /** @const array<int, string> Order of this array is very important */
    public const IBEXA_PRODUCTS = [
        'ibexa/commerce',
        'ibexa/experience',
        'ibexa/headless',
        'ibexa/oss',
    ];

    public function onCommand(ConsoleCommandEvent $event): void
    {
        $command = $event->getCommand();

        if (!$command instanceof InstallPlatformCommand) {
            return;
        }

        if (!$event->getInput()->getArgument('type')) {
            $event->getOutput()->writeln(
                'No "type" argument passed. '
                . 'Installer will automatically choose installation '
                . 'type relevant to your product edition'
            );
        }

        $definition = $command->getDefinition();
        $arguments = $definition->getArguments();
        $typeArgument = $arguments['type'];
        $newTypeArgument = new InputArgument(
            'type',
            InputArgument::OPTIONAL,
            $typeArgument->getDescription(),
            $this->getInstallerType()
        );
        $definition->setArguments([$newTypeArgument]);
        $command->setDefinition($definition);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ConsoleEvents::COMMAND => ['onCommand', 0],
        ];
    }

    private function getInstallerType(): string
    {
        return str_replace('/', '-', $this->getInstalledIbexaProduct());
    }

    private function getInstalledIbexaProduct(): string
    {
        $packages = InstalledVersions::getInstalledPackages();
        $ibexaPackages = array_filter($packages, static function (string $packageName): bool {
            return strpos($packageName, 'ibexa/') !== false;
        });

        // removes unrelated Ibexa packages
        $installedIbexaProducts = array_values(array_intersect($ibexaPackages, self::IBEXA_PRODUCTS));

        // sorts $installedIbexaProducts according to the order of self::IBEXA_PRODUCTS
        $installedIbexaProducts = array_keys(
            array_filter(
                array_replace(
                    array_fill_keys(self::IBEXA_PRODUCTS, false),
                    array_fill_keys($installedIbexaProducts, true)
                )
            )
        );

        // first element in the array is the package matching product edition
        return reset($installedIbexaProducts);
    }
}

class_alias(InstallCommandSubscriber::class, 'Ibexa\Platform\Installer\Event\Subscriber\InstallCommandSubscriber');
