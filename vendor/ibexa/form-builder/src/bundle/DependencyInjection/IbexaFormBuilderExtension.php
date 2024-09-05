<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\FormBuilder\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Yaml\Yaml;

class IbexaFormBuilderExtension extends Extension implements PrependExtensionInterface
{
    public const EXTENSION_NAME = 'ibexa_form_builder';

    public function getAlias(): string
    {
        return self::EXTENSION_NAME;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('ibexa.form_builder.fields', $config['fields']);

        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );

        $loader->load('services.yaml');

        if ($this->shouldLoadTestServices($container)) {
            $loader->load('services/tests/data_provider.yaml');
            $loader->load('services/tests/feature_context.yaml');
            $loader->load('services/tests/form_fields.yaml');
            $loader->load('services/tests/components.yaml');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function prepend(ContainerBuilder $container): void
    {
        $this->prependViews($container);
        $this->prependFormBuilder($container);
        $this->prependFieldType($container);
        $this->prependJMSTranslation($container);
        $this->prependBazingaJsTranslationConfiguration($container);
        $this->prependUniversalDiscoveryWidget($container);
        $this->prependBlocks($container);
        $this->prependPageBuilderForms($container);
    }

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    private function prependFormBuilder(ContainerBuilder $container): void
    {
        $config = Yaml::parseFile(
            __DIR__ . '/../Resources/config/field_definitions.yaml'
        );

        $container->prependExtensionConfig('ibexa_form_builder', $config);
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
    private function prependFieldType(ContainerBuilder $container): void
    {
        $config = Yaml::parseFile(
            __DIR__ . '/../Resources/config/ez_field_templates.yaml'
        );

        $container->prependExtensionConfig('ibexa', $config);
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
                        __DIR__ . '/../../',
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
        $container->prependExtensionConfig('bazinga_js_translation', [
            'active_domains' => [
                'ibexa_form_builder',
            ],
        ]);
    }

    private function shouldLoadTestServices(ContainerBuilder $container): bool
    {
        return $container->hasParameter('ibexa.behat.browser.enabled')
            && $container->getParameter('ibexa.behat.browser.enabled') === true;
    }
}

class_alias(IbexaFormBuilderExtension::class, 'EzSystems\EzPlatformFormBuilderBundle\DependencyInjection\EzPlatformFormBuilderExtension');
