<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\PageBuilder\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Yaml\Yaml;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class IbexaPageBuilderExtension extends Extension implements PrependExtensionInterface
{
    public const EXTENSION_NAME = 'ibexa_page_builder';

    /** @deprecated Deprecated since Ibexa DXP 4.5.0. */
    public const SESSION_KEY_SITEACCESS = 'ezplatform.page_builder.siteaccess';
    public const ENABLE_TOKEN_AUTHENTICATION = 'ibexa.page_builder.token_authenticator.enabled';

    public function getAlias(): string
    {
        return self::EXTENSION_NAME;
    }

    /**
     * @param array $configs
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     *
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yaml');

        if ($this->isTokenAuthEnabled($container)) {
            $loader->load('security.yaml');
        }

        $shouldLoadTestServices = $this->shouldLoadTestServices($container);
        if ($shouldLoadTestServices) {
            $this->loadTestServices($loader);
        }
    }

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     *
     * @return bool
     */
    private function isTokenAuthEnabled(ContainerBuilder $container): bool
    {
        if (!$container->hasParameter(self::ENABLE_TOKEN_AUTHENTICATION)) {
            return false;
        }

        return (bool)$container->getParameter(self::ENABLE_TOKEN_AUTHENTICATION);
    }

    /**
     * Allow an extension to prepend the extension configurations.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function prepend(ContainerBuilder $container): void
    {
        $this->prependViews($container);
        $this->prependBlocks($container);
        $this->prependAdminUiForms($container);
        $this->prependPageBuilderForms($container);
        $this->prependJMSTranslation($container);
        $this->prependBazingaJsTranslationConfiguration($container);
        $this->prependPageBuilderConfig($container);
        $this->prependUniversalDiscoveryWidget($container);
    }

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    private function prependBlocks(ContainerBuilder $container): void
    {
        $configFile = __DIR__ . '/../Resources/config/blocks.yaml';
        $config = Yaml::parse(file_get_contents($configFile));
        $container->prependExtensionConfig('ibexa_fieldtype_page', $config);
        $container->addResource(new FileResource($configFile));
    }

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    private function prependViews(ContainerBuilder $container): void
    {
        $configFile = __DIR__ . '/../Resources/config/views.yaml';
        $config = Yaml::parse(file_get_contents($configFile));
        $container->prependExtensionConfig('ibexa', $config);
        $container->addResource(new FileResource($configFile));
    }

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    private function prependAdminUiForms(ContainerBuilder $container): void
    {
        $configFile = __DIR__ . '/../Resources/config/admin_ui_forms.yaml';
        $config = Yaml::parse(file_get_contents($configFile));
        $container->prependExtensionConfig('ibexa', $config);
        $container->addResource(new FileResource($configFile));
    }

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    private function prependPageBuilderForms(ContainerBuilder $container): void
    {
        $configFile = __DIR__ . '/../Resources/config/page_builder_forms.yaml';
        $config = Yaml::parse(file_get_contents($configFile));
        $container->prependExtensionConfig('ibexa', $config);
        $container->addResource(new FileResource($configFile));
    }

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function prependJMSTranslation(ContainerBuilder $container): void
    {
        $container->prependExtensionConfig('jms_translation', [
            'configs' => [
                self::EXTENSION_NAME => [
                    'dirs' => [
                        __DIR__ . '/../../../src',
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

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    private function prependBazingaJsTranslationConfiguration(ContainerBuilder $container): void
    {
        $configFile = __DIR__ . '/../Resources/config/bazinga_js_translation.yaml';
        $config = Yaml::parseFile($configFile);
        $container->prependExtensionConfig('bazinga_js_translation', $config);
        $container->addResource(new FileResource($configFile));
    }

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    private function prependPageBuilderConfig(ContainerBuilder $container): void
    {
        $configFile = __DIR__ . '/../Resources/config/default_config.yaml';
        $container->prependExtensionConfig(self::EXTENSION_NAME, Yaml::parseFile($configFile));
        $container->addResource(new FileResource($configFile));
    }

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    private function prependUniversalDiscoveryWidget(ContainerBuilder $container): void
    {
        $configFile = __DIR__ . '/../Resources/config/universal_discovery_widget.yaml';
        $config = Yaml::parse(file_get_contents($configFile));
        $container->prependExtensionConfig('ibexa', $config);
        $container->addResource(new FileResource($configFile));
    }

    private function shouldLoadTestServices(ContainerBuilder $container): bool
    {
        return $container->hasParameter('ibexa.behat.browser.enabled')
            && $container->getParameter('ibexa.behat.browser.enabled') === true;
    }

    private function loadTestServices(YamlFileLoader $loader): void
    {
        $loader->load('services/test/feature_contexts.yaml');
        $loader->load('services/test/pages.yaml');
        $loader->load('services/test/components.yaml');
        $loader->load('services/test/fieldtype_data_provider.yaml');
        $loader->load('services/test/blocks.yaml');
        $loader->load('services/test/previews.yaml');
        $loader->load('services/test/environment.yaml');
        $loader->load('services/test/layouts.yaml');
    }
}

class_alias(IbexaPageBuilderExtension::class, 'EzSystems\EzPlatformPageBuilderBundle\DependencyInjection\EzPlatformPageBuilderExtension');
