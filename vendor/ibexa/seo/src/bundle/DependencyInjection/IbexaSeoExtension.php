<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Seo\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Yaml\Yaml;

final class IbexaSeoExtension extends Extension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );

        $loader->load('services.yaml');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('ibexa.seo.types', $config['types'] ?? []);
    }

    public function prepend(ContainerBuilder $container): void
    {
        $this->prependJMSTranslation($container);
        $this->prependBuiltInTypesConfig($container);
        $this->prependIbexaCoreConfiguration($container);
        $this->prependViews($container);
        $this->prependAdminUiForms($container);
    }

    private function prependJMSTranslation(ContainerBuilder $container): void
    {
        $container->prependExtensionConfig('jms_translation', [
            'configs' => [
                'ibexa_seo' => [
                    'dirs' => [
                        __DIR__ . '/../../',
                    ],
                    'output_dir' => __DIR__ . '/../Resources/translations/',
                    'output_format' => 'xliff',
                ],
            ],
        ]);
    }

    private function prependBuiltInTypesConfig(ContainerBuilder $container): void
    {
        $builtInTypesConfig = dirname(__DIR__) . '/Resources/config/builtin_types.yaml';
        $config = Yaml::parseFile($builtInTypesConfig);
        /** @var array<string, mixed> $config */
        $container->prependExtensionConfig('ibexa_seo', $config);
        $container->addResource(new FileResource($builtInTypesConfig));
    }

    private function prependIbexaCoreConfiguration(ContainerBuilder $container): void
    {
        $coreExtensionConfigFile = dirname(__DIR__) . '/Resources/config/default_enabled_types.yaml';
        $config = Yaml::parseFile($coreExtensionConfigFile);
        /** @var array<string, mixed> $config */
        $container->prependExtensionConfig('ibexa', $config);
        $container->addResource(new FileResource($coreExtensionConfigFile));
    }

    public function prependViews(ContainerBuilder $container): void
    {
        $configFile = __DIR__ . '/../Resources/config/prepend/views.yaml';
        /** @var array<string, mixed> $config */
        $config = Yaml::parseFile($configFile);
        $container->prependExtensionConfig('ibexa', $config);
        $container->addResource(new FileResource($configFile));
    }

    public function prependAdminUiForms(ContainerBuilder $container): void
    {
        $adminUiFormsConfigFile = __DIR__ . '/../Resources/config/admin_ui_forms.yaml';
        /** @var array<string, mixed> $config */
        $config = Yaml::parseFile($adminUiFormsConfigFile);
        $container->prependExtensionConfig('ibexa', $config);
        $container->addResource(new FileResource($adminUiFormsConfigFile));
    }
}
