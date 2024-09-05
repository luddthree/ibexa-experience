<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Dashboard\DependencyInjection;

use Ibexa\Contracts\Dashboard\Block;
use LogicException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Yaml\Yaml;

final class IbexaDashboardExtension extends Extension implements PrependExtensionInterface
{
    public const BLOCK_QUICK_ACTIONS_ACTION_TAG = 'ibexa.dashboard.block.quick_actions.action';

    /**
     * @param array<string, mixed> $configs
     *
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );

        $loader->load('services.yaml');

        $container
            ->registerForAutoconfiguration(Block\QuickActions\ActionInterface::class)
            ->addTag(self::BLOCK_QUICK_ACTIONS_ACTION_TAG);

        if ($this->shouldLoadTestServices($container)) {
            $loader->load('test/pages.yaml');
        }
    }

    public function prepend(ContainerBuilder $container): void
    {
        $this->prependDefaultConfiguration($container);
        $this->prependJMSTranslation($container);
    }

    private function prependDefaultConfiguration(ContainerBuilder $container): void
    {
        $configFiles = glob(__DIR__ . '/../Resources/config/prepend/*.yaml');
        if ($configFiles === false) {
            throw new LogicException('IbexaDashboardExtension: Failed to find any configuration to prepend');
        }

        foreach ($configFiles as $configFile) {
            $container->addResource(new FileResource($configFile));
            $configs = Yaml::parseFile($configFile, Yaml::PARSE_CONSTANT) ?? [];
            foreach ($configs as $name => $config) {
                $container->prependExtensionConfig($name, $config);
            }
        }
    }

    private function prependJMSTranslation(ContainerBuilder $container): void
    {
        $container->prependExtensionConfig('jms_translation', [
            'configs' => [
                'ibexa_dashboard' => [
                    'dirs' => [
                        __DIR__ . '/../../',
                    ],
                    'excluded_dirs' => ['Behat'],
                    'output_dir' => __DIR__ . '/../Resources/translations/',
                    'output_format' => 'xliff',
                ],
            ],
        ]);
    }

    private function shouldLoadTestServices(ContainerBuilder $container): bool
    {
        return $container->hasParameter('ibexa.behat.browser.enabled')
            && true === $container->getParameter('ibexa.behat.browser.enabled');
    }
}
