<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\FieldTypePage\DependencyInjection;

use Ibexa\FieldTypePage\FieldType\LandingPage\Definition\LayoutDefinition;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;
use Symfony\Component\Yaml\Yaml;

/**
 * @phpstan-import-type TBlockConfiguration from \Ibexa\Bundle\FieldTypePage\DependencyInjection\Configuration
 * @phpstan-import-type TReactBlockConfiguration from \Ibexa\Bundle\FieldTypePage\DependencyInjection\Configuration
 * @phpstan-import-type TLayout from \Ibexa\Bundle\FieldTypePage\DependencyInjection\Configuration
 *
 * This is the class that loads and manages your bundle configuration.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class IbexaFieldTypePageExtension extends ConfigurableExtension implements PrependExtensionInterface
{
    public const EXTENSION_NAME = 'ibexa_fieldtype_page';

    /**
     * {@inheritdoc}
     */
    public function getAlias(): string
    {
        return self::EXTENSION_NAME;
    }

    /**
     * @phpstan-param array{
     *     blocks?: array<string, TBlockConfiguration>,
     *     block_validators: array<string, class-string<\Symfony\Component\Validator\Constraint>>,
     *     layouts?: array<string, TLayout>,
     *     react_blocks?: array<TReactBlockConfiguration>,
     * } $mergedConfig
     */
    protected function loadInternal(array $mergedConfig, ContainerBuilder $container): void
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yaml');

        $shouldLoadTestServices = $this->shouldLoadTesttServices($container);
        if ($shouldLoadTestServices) {
            $loader->load('services/test/feature_contexts.yaml');
        }

        $bundles = $container->getParameter('kernel.bundles');
        if (isset($bundles['IbexaGraphQLBundle'])) {
            $this->loadGraphQlServices($container);
        }

        if (isset($mergedConfig['layouts'])) {
            $this->loadLayoutDefinitions($container, $mergedConfig['layouts']);
        }

        if (isset($mergedConfig['blocks'])) {
            $container->setParameter('ibexa.field_type.page.block.config', $mergedConfig['blocks']);
        }

        if (isset($mergedConfig['react_blocks'])) {
            $container->setParameter('ibexa.field_type.page.react_blocks.config', $mergedConfig['react_blocks']);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Symfony\Component\Yaml\Exception\ParseException
     */
    public function prepend(ContainerBuilder $container): void
    {
        $config = Yaml::parse(file_get_contents(__DIR__ . '/../Resources/config/ez_field_templates.yaml'));
        $container->prependExtensionConfig('ibexa', $config);

        $config = Yaml::parse(file_get_contents(__DIR__ . '/../Resources/config/image_variations.yaml'));
        $container->prependExtensionConfig('ibexa', $config);

        $config = Yaml::parse(file_get_contents(__DIR__ . '/../Resources/config/blocks.yaml'));
        $container->prependExtensionConfig(self::EXTENSION_NAME, $config);

        $config = Yaml::parse(file_get_contents(__DIR__ . '/../Resources/config/layouts.yaml'));
        $container->prependExtensionConfig(self::EXTENSION_NAME, $config);

        $config = Yaml::parse(file_get_contents(__DIR__ . '/../Resources/config/validators.yaml'));
        $container->prependExtensionConfig(self::EXTENSION_NAME, $config);

        $container->prependExtensionConfig('overblog_graphql', [
            'definitions' => [
                'mappings' => [
                    'types' => [
                        [
                            'type' => 'yaml',
                            'dir' => __DIR__ . '/../Resources/config/graphql/',
                        ],
                    ],
                ],
            ],
        ]);

        $this->prependCalendarConfiguration($container);
        $this->prependJMSTranslation($container);
        $this->prependBazingaJsTranslationConfiguration($container);
    }

    /**
     * Loads layout definitions.
     *
     * @phpstan-param array<string, TLayout> $layouts
     */
    public function loadLayoutDefinitions(ContainerBuilder $container, array $layouts): void
    {
        foreach ($layouts as $key => $layout) {
            $definition = $container->register(
                'ezpublish.landing_page.layout_definition.' . $key,
                LayoutDefinition::class
            );

            $definition->addTag('ibexa.field_type.page.layout');

            $definition->setArguments([
                $layout['identifier'],
                $layout['name'],
                $layout['description'],
                $layout['thumbnail'],
                $layout['template'],
                $layout['zones'],
                $layout['visible'],
            ]);
        }
    }

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     *
     * @throws \Exception
     */
    private function loadGraphQlServices(ContainerBuilder $container): void
    {
        $loader = new Loader\YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config/services/graphql')
        );

        $loader->load('common.yaml');
        $loader->load('attributes_builders.yaml');
        $loader->load('schema.yaml');
        $loader->load('workers.yaml');
        $loader->load('resolvers.yaml');
    }

    private function prependCalendarConfiguration(ContainerBuilder $container): void
    {
        $configFile = __DIR__ . '/../Resources/config/calendar.yaml';
        $config = Yaml::parseFile($configFile);
        $container->prependExtensionConfig('ibexa', $config);
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

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
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

    private function shouldLoadTesttServices(ContainerBuilder $container): bool
    {
        return $container->hasParameter('ibexa.behat.browser.enabled')
            && true === $container->getParameter('ibexa.behat.browser.enabled');
    }
}

class_alias(IbexaFieldTypePageExtension::class, 'EzSystems\EzPlatformPageFieldTypeBundle\DependencyInjection\EzPlatformPageFieldTypeExtension');
