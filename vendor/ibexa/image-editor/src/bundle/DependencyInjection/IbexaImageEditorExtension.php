<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ImageEditor\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Yaml\Yaml;

final class IbexaImageEditorExtension extends Extension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );
        $loader->load('services.yaml');
        $loader->load('components.yaml');
    }

    public function prepend(ContainerBuilder $container): void
    {
        $this->prependEzPlatformConfiguration($container);
        $this->prependAdminUiFormTemplates($container);
        $this->prependJMSTranslation($container);
        $this->prependBazingaJsTranslationConfiguration($container);
    }

    private function prependAdminUiFormTemplates(ContainerBuilder $container): void
    {
        $formTemplatesConfigFile = __DIR__ . '/../Resources/config/admin_ui_forms.yaml';
        $config = Yaml::parseFile($formTemplatesConfigFile);
        $container->prependExtensionConfig('ibexa', $config);
        $container->addResource(new FileResource($formTemplatesConfigFile));
    }

    private function prependEzPlatformConfiguration(ContainerBuilder $container): void
    {
        $coreExtensionConfigFile = realpath(__DIR__ . '/../Resources/config/default_settings.yaml');
        $container->prependExtensionConfig('ibexa', Yaml::parseFile($coreExtensionConfigFile));
        $container->addResource(new FileResource($coreExtensionConfigFile));
    }

    private function prependJMSTranslation(ContainerBuilder $container): void
    {
        $container->prependExtensionConfig('jms_translation', [
            'configs' => [
                'ibexa_image_editor' => [
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

    private function prependBazingaJsTranslationConfiguration(ContainerBuilder $container): void
    {
        $configFile = __DIR__ . '/../Resources/config/bazinga_js_translation.yaml';
        $config = Yaml::parseFile($configFile);
        $container->prependExtensionConfig('bazinga_js_translation', $config);
        $container->addResource(new FileResource($configFile));
    }
}

class_alias(IbexaImageEditorExtension::class, 'Ibexa\Platform\Bundle\ImageEditor\DependencyInjection\IbexaPlatformImageEditorExtension');
