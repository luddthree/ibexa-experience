<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\CorporateAccount\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;
use Symfony\Component\Yaml\Yaml;

final class IbexaCorporateAccountExtension extends ConfigurableExtension implements PrependExtensionInterface
{
    /**
     * @param array<string, mixed> $mergedConfig
     */
    protected function loadInternal(
        array $mergedConfig,
        ContainerBuilder $container
    ): void {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );

        $loader->load('services.yaml');
        $loader->load('default_settings.yaml');
        $loader->load('content_view.yaml');

        $container->setParameter('ibexa.corporate_account', $mergedConfig);

        if ($this->shouldLoadTestServices($container)) {
            $loader->load('test/feature_contexts.yaml');
            $loader->load('test/pages.yaml');
        }
    }

    public function prepend(ContainerBuilder $container): void
    {
        $this->prependCorporateAccountSettings($container);
        $this->prependBazingaJsTranslationConfiguration($container);
        $this->prependJMSTranslation($container);
        $this->prependIbexaDesignConfiguration($container);
        $this->prependFieldTypeAddressFormats($container);
        $this->prependPageBlocks($container);
    }

    private function prependIbexaDesignConfiguration(ContainerBuilder $container): void
    {
        $resource = __DIR__ . '/../Resources/config/ibexadesign.yaml';
        $config = Yaml::parseFile($resource);
        $container->prependExtensionConfig('ibexa_design_engine', $config['ibexa_design_engine']);
        $container->prependExtensionConfig('ibexa', $config['ibexa']);
        $container->addResource(new FileResource($resource));
    }

    private function prependCorporateAccountSettings(ContainerBuilder $container): void
    {
        $configFile = __DIR__ . '/../Resources/config/ibexa_corporate_account.yaml';
        $config = Yaml::parseFile($configFile);
        $container->prependExtensionConfig($this->getAlias(), $config['ibexa_corporate_account']);
        $container->addResource(new FileResource($configFile));
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

    private function prependFieldTypeAddressFormats(ContainerBuilder $container): void
    {
        $configFile = __DIR__ . '/../Resources/config/fieldtype_address.yaml';
        $config = Yaml::parseFile($configFile);
        $container->prependExtensionConfig('ibexa_field_type_address', $config);
        $container->addResource(new FileResource($configFile));
    }

    private function prependPageBlocks(ContainerBuilder $container): void
    {
        $configFile = __DIR__ . '/../Resources/config/page_builder/blocks.yaml';
        $blockConfig = Yaml::parseFile($configFile);
        $container->prependExtensionConfig('ibexa_fieldtype_page', $blockConfig);
        $container->addResource(new FileResource($configFile));
    }

    private function shouldLoadTestServices(ContainerBuilder $container): bool
    {
        return $container->hasParameter('ibexa.behat.browser.enabled')
            && true === $container->getParameter('ibexa.behat.browser.enabled');
    }

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function prependJMSTranslation(ContainerBuilder $container): void
    {
        $container->prependExtensionConfig('jms_translation', [
            'configs' => [
                'ibexa_corporate_account' => [
                    'dirs' => [
                        __DIR__ . '/../../',
                    ],
                    'excluded_dirs' => ['Behat', 'Tests', 'Features', 'Cdp'],
                    'output_dir' => __DIR__ . '/../Resources/translations/',
                    'output_format' => 'xliff',
                ],
            ],
        ]);
    }
}
