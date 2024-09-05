<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\Bundle\Scheduler\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Yaml\Yaml;

/**
 * Loads and manages bundle configuration.
 */
class IbexaSchedulerExtension extends Extension implements PrependExtensionInterface
{
    public const EXTENSION_NAME = 'ibexa_scheduler';

    public function getAlias(): string
    {
        return self::EXTENSION_NAME;
    }

    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yaml');
        $loader->load('rest_services.yaml');
        $loader->load('default_settings.yaml');

        $isPageBuilderEnabled = $this->isPageBuilderEnabled($container);
        if ($isPageBuilderEnabled) {
            $loader->load('page_builder.yaml');
        }

        $shouldLoadTestServices = $this->shouldLoadTestServices($container);
        if ($shouldLoadTestServices) {
            $loader->load('test/feature_contexts.yaml');
            $loader->load('test/components.yaml');
        }

        if ($isPageBuilderEnabled && $shouldLoadTestServices) {
            $loader->load('test/feature_contexts_experience.yaml');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function prepend(ContainerBuilder $container): void
    {
        $container->prependExtensionConfig('assetic', ['bundles' => ['IbexaSchedulerBundle']]);

        // Directory where public resources are stored (relative to web/ directory).
        $container->setParameter('ibexa.scheduler.public_dir', 'bundles/ezsystemsdatebasedpublisher');

        if ($this->isPageBuilderEnabled($container)) {
            $this->prependPageBuilderConfig($container);
        }

        $this->prependJMSTranslation($container);
        $this->prependBazingaJsTranslationConfiguration($container);
        $this->prependCalendarConfiguration($container);
    }

    private function prependPageBuilderConfig(ContainerBuilder $container): void
    {
        $configFile = __DIR__ . '/../Resources/config/ezplatform_page_builder.yaml';
        $container->addResource(new FileResource($configFile));
        $container->prependExtensionConfig('ibexa_page_builder', Yaml::parseFile($configFile));
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
                    'excluded_dirs' => ['grunt', 'Features', 'Behat'],
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

    private function prependCalendarConfiguration(ContainerBuilder $container): void
    {
        $configFile = __DIR__ . '/../Resources/config/calendar.yaml';
        $config = Yaml::parseFile($configFile);
        $container->prependExtensionConfig('ibexa', $config);
        $container->addResource(new FileResource($configFile));
    }

    private function shouldLoadTestServices(ContainerBuilder $container): bool
    {
        return $container->hasParameter('ibexa.behat.browser.enabled')
            && true === $container->getParameter('ibexa.behat.browser.enabled');
    }

    private function isPageBuilderEnabled(ContainerBuilder $containerBuilder): bool
    {
        $bundles = $containerBuilder->getParameter('kernel.bundles');

        return isset($bundles['IbexaPageBuilderBundle']);
    }
}

class_alias(IbexaSchedulerExtension::class, 'EzSystems\DateBasedPublisherBundle\DependencyInjection\EzSystemsDateBasedPublisherExtension');
