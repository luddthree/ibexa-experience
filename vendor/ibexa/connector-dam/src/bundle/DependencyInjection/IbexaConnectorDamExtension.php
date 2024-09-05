<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Connector\Dam\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Yaml\Yaml;

final class IbexaConnectorDamExtension extends Extension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yaml');
        $loader->load('field_type.yaml');
        $loader->load('views.yaml');
        $loader->load('variations.yaml');
    }

    public function prepend(ContainerBuilder $container): void
    {
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

    private function prependJMSTranslation(ContainerBuilder $container): void
    {
        $container->prependExtensionConfig('jms_translation', [
            'configs' => [
                'ibexa_connector_dam' => [
                    'dirs' => [
                        __DIR__ . '/../../',
                    ],
                    'output_dir' => __DIR__ . '/../Resources/translations/',
                    'output_format' => 'xliff',
                    'excluded_names' => ['*.module.js'],
                    'ignored_domains' => ['ibexa_content_fields', 'ibexa_content_forms_content', 'ibexa_fieldtypes_preview'],
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

class_alias(IbexaConnectorDamExtension::class, 'Ibexa\Platform\Bundle\Connector\Dam\DependencyInjection\IbexaPlatformConnectorDamExtension');
