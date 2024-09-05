<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Personalization\DependencyInjection;

use Ibexa\Contracts\Personalization\Storage\DataSourceInterface;
use Ibexa\Personalization\Exception\ReadFileContentFailedException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\FileLoader;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Yaml\Yaml;

final class IbexaPersonalizationExtension extends Extension implements PrependExtensionInterface
{
    public const EXTENSION_NAME = 'ibexa_personalization';
    public const DATA_SOURCE_SERVICE_TAG = 'ibexa.personalization.data_source';

    public function getAlias(): string
    {
        return self::EXTENSION_NAME;
    }

    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = $this->getFileLoader($container);
        $loader->load('services.yaml');

        $container
            ->registerForAutoconfiguration(DataSourceInterface::class)
            ->addTag(self::DATA_SOURCE_SERVICE_TAG);
    }

    public function prepend(ContainerBuilder $container): void
    {
        $this->prependEzDesignSettings($container);
        $this->prependDefaultSettings($container);
        $this->prependUniversalDiscoveryWidget($container);
        $this->prependBazingaJsTranslationConfiguration($container);
        $this->prependJMSTranslation($container);

        if ($this->hasPageBuilderBundle($container)) {
            $loader = $this->getFileLoader($container);
            $loader->load('services/page_builder.yaml');

            $this->prependFormTemplateSettings($container);
            $this->prependPageBlock($container);
        }
    }

    private function prependDefaultSettings(ContainerBuilder $container): void
    {
        $configFile = __DIR__ . '/../Resources/config/defaults/templates.yaml';
        $config = Yaml::parseFile($configFile);
        $container->prependExtensionConfig('ibexa', $config);
        $container->addResource(new FileResource($configFile));
    }

    private function prependEzDesignSettings(ContainerBuilder $container): void
    {
        $eZDesignConfigFile = __DIR__ . '/../Resources/config/ezdesign.yaml';
        $config = Yaml::parseFile($eZDesignConfigFile);
        $container->prependExtensionConfig('ibexa_design_engine', $config['ibexa_design_engine']);
        $container->addResource(new FileResource($eZDesignConfigFile));
    }

    private function prependUniversalDiscoveryWidget(ContainerBuilder $container): void
    {
        $configFile = __DIR__ . '/../Resources/config/universal_discovery_widget.yaml';
        $config = Yaml::parse(file_get_contents($configFile));
        $container->prependExtensionConfig('ibexa', $config);
        $container->addResource(new FileResource($configFile));
    }

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    private function prependBazingaJsTranslationConfiguration(ContainerBuilder $container)
    {
        $configFile = __DIR__ . '/../Resources/config/bazinga_js_translation.yaml';
        $config = Yaml::parseFile($configFile);
        $container->prependExtensionConfig('bazinga_js_translation', $config);
        $container->addResource(new FileResource($configFile));
    }

    private function prependJMSTranslation(ContainerBuilder $container): void
    {
        $container->prependExtensionConfig('jms_translation', [
            'configs' => [
                self::EXTENSION_NAME => [
                    'dirs' => [
                        __DIR__ . '/../../../src/',
                    ],
                    'output_dir' => __DIR__ . '/../Resources/translations/',
                    'output_format' => 'xliff',
                    'excluded_dirs' => ['Behat', 'Tests', 'node_modules'],
                    'extractors' => [],
                ],
            ],
        ]);
    }

    private function prependFormTemplateSettings(ContainerBuilder $container): void
    {
        $configFile = __DIR__ . '/../Resources/config/page_builder/form_templates.yaml';
        $config = Yaml::parseFile($configFile);
        $container->prependExtensionConfig('ibexa', $config);
        $container->addResource(new FileResource($configFile));
    }

    private function prependPageBlock(ContainerBuilder $container): void
    {
        $configFile = __DIR__ . '/../Resources/config/blocks.yaml';
        $blockValidatorsConfigFile = __DIR__ . '/../Resources/config/block_validators.yaml';

        $configFileContent = file_get_contents($configFile);
        $blockValidatorsFileContent = file_get_contents($blockValidatorsConfigFile);

        if (false === $configFileContent) {
            throw new ReadFileContentFailedException($configFile);
        }

        if (false === $blockValidatorsFileContent) {
            throw new ReadFileContentFailedException($blockValidatorsConfigFile);
        }

        $blockConfig = Yaml::parse($configFileContent);
        $blockValidatorsConfig = Yaml::parse($blockValidatorsFileContent);

        $container->prependExtensionConfig('ibexa_fieldtype_page', $blockConfig);
        $container->prependExtensionConfig('ibexa_fieldtype_page', $blockValidatorsConfig);
        $container->addResource(new FileResource($configFile));
        $container->addResource(new FileResource($blockValidatorsConfigFile));
    }

    private function getFileLoader(ContainerBuilder $container): FileLoader
    {
        return new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
    }

    private function hasPageBuilderBundle(ContainerBuilder $container): bool
    {
        return $container->hasExtension('ibexa_page_builder');
    }
}

class_alias(IbexaPersonalizationExtension::class, 'Ibexa\Platform\Bundle\Personalization\DependencyInjection\IbexaPlatformPersonalizationExtension');
