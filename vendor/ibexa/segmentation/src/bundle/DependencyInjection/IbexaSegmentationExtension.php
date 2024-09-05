<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Segmentation\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Yaml\Yaml;

final class IbexaSegmentationExtension extends Extension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );

        $loader->load('services.yaml');

        $shouldLoadTestServices = $this->shouldLoadTestServices($container);
        if ($shouldLoadTestServices) {
            $loader->load('services/test/feature_contexts.yaml');
            $loader->load('services/test/pages.yaml');
            $loader->load('services/test/components.yaml');
        }
    }

    public function prepend(ContainerBuilder $container)
    {
        $config = Yaml::parse(file_get_contents(
            __DIR__ . '/../Resources/config/page_builder/blocks.yaml'
        ));
        $container->prependExtensionConfig('ibexa_fieldtype_page', $config);

        $config = Yaml::parse(file_get_contents(
            __DIR__ . '/../Resources/config/page_builder/form_templates.yaml'
        ));
        $container->prependExtensionConfig('ibexa', $config);

        $this->prependDefaultSettings($container);
        $this->prependBazingaJsTranslationConfiguration($container);
        $this->prependJMSTranslation($container);
    }

    private function prependDefaultSettings(ContainerBuilder $container): void
    {
        $configFile = __DIR__ . '/../Resources/config/limitation_value_templates.yaml';
        $config = Yaml::parseFile($configFile);
        $container->prependExtensionConfig('ibexa', $config);
        $container->addResource(new FileResource($configFile));
    }

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
                'ibexa_segmentation' => [
                    'dirs' => [
                        __DIR__ . '/../../../src/',
                    ],
                    'output_dir' => __DIR__ . '/../Resources/translations/',
                    'output_format' => 'xliff',
                    'excluded_dirs' => ['Behat', 'Tests', 'node_modules'],
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

class_alias(IbexaSegmentationExtension::class, 'Ibexa\Platform\Bundle\Segmentation\DependencyInjection\IbexaPlatformSegmentationExtension');
