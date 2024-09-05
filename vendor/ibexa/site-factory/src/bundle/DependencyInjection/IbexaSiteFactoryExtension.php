<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\SiteFactory\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Yaml\Yaml;

final class IbexaSiteFactoryExtension extends Extension implements PrependExtensionInterface
{
    public const EXTENSION_NAME = 'ibexa_site_factory';

    public function getAlias(): string
    {
        return self::EXTENSION_NAME;
    }

    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('ibexa.site_factory.enabled', $config['enabled']);
        $container->setParameter('ibexa.site_factory.update_roles', $config['update_roles']);
        $container->setParameter('ibexa.site_factory.templates', $config['templates']);

        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );

        $environment = $container->getParameter('kernel.environment');

        if ($environment === 'behat') {
            $container->setParameter('ibexa.site_factory.enabled', true);
            $loader->load('services/feature_contexts.yaml');
        }

        $loader->load('services.yaml');

        if ($container->getParameter('ibexa.site_factory.enabled')) {
            $loader->load('services/site_factory.yaml');
        }
    }

    public function prepend(ContainerBuilder $container): void
    {
        $this->prependJMSTranslation($container);
        $this->prependSettings($container);
        $this->prependBazingaJsTranslationConfiguration($container);
    }

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    private function prependSettings(ContainerBuilder $container): void
    {
        $config = Yaml::parseFile(
            __DIR__ . '/../Resources/config/settings.yaml'
        );

        $container->prependExtensionConfig('ibexa_site_factory', $config);
    }

    private function prependJMSTranslation(ContainerBuilder $container): void
    {
        $container->prependExtensionConfig('jms_translation', [
            'configs' => [
                self::EXTENSION_NAME => [
                    'dirs' => [
                        __DIR__ . '/../../bundle/',
                        __DIR__ . '/../../lib/',
                    ],
                    'output_dir' => __DIR__ . '/../Resources/translations/',
                    'output_format' => 'xliff',
                    'excluded_names' => ['*.module.js'],
                    'excluded_dirs' => ['Behat', 'Tests', 'node_modules'],
                    'extractors' => [],
                ],
            ],
        ]);
    }

    private function prependBazingaJsTranslationConfiguration(ContainerBuilder $container): void
    {
        $configFile = __DIR__ . '/../Resources/config/bazinga_js_translation.yaml';
        $config = Yaml::parseFile($configFile);
        $container->prependExtensionConfig('bazinga_js_translation', $config);
        $container->addResource(new FileResource($configFile));
    }
}

class_alias(IbexaSiteFactoryExtension::class, 'EzSystems\EzPlatformSiteFactoryBundle\DependencyInjection\EzPlatformSiteFactoryExtension');
