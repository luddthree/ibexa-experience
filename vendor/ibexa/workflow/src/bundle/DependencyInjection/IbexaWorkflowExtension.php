<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Workflow\DependencyInjection;

use Ibexa\Bundle\Core\DependencyInjection\Configuration\SiteAccessAware\ConfigurationProcessor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Yaml\Yaml;

class IbexaWorkflowExtension extends Extension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yaml');

        $shouldLoadTestServices = $this->shouldLoadTestServices($container);
        if ($shouldLoadTestServices) {
            $loader->load('services/test/feature_contexts.yaml');
            $loader->load('services/test/pages.yaml');
            $loader->load('services/test/facade.yaml');
            $loader->load('services/test/components.yaml');
        }

        $hasPageBuilderBundle = $this->hasPageBuilderBundle($container);

        if ($hasPageBuilderBundle) {
            $loader->load('services/page_builder.yaml');
        }

        if ($shouldLoadTestServices && $hasPageBuilderBundle) {
            $loader->load('services/test/feature_contexts_experience.yaml');
        }

        return new ConfigurationProcessor($container, 'ibexa_workflow');
    }

    public function prepend(ContainerBuilder $container): void
    {
        $container->prependExtensionConfig('assetic', ['bundles' => ['IbexaWorkflowBundle']]);

        $this->prependDefaultSettings($container);
        $this->prependEzDesignSettings($container);
        $this->prependAdminUiFormTemplates($container);
        $this->prependQuickReviewSettings($container);
        $this->prependBazingaJsTranslationConfiguration($container);
        $this->prependJMSTranslation($container);
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

    private function prependAdminUiFormTemplates(ContainerBuilder $container): void
    {
        $formTemplatesConfigFile = __DIR__ . '/../Resources/config/admin_ui_forms.yaml';
        $config = Yaml::parseFile($formTemplatesConfigFile);
        $container->prependExtensionConfig('ibexa', $config);
        $container->addResource(new FileResource($formTemplatesConfigFile));
    }

    private function prependBazingaJsTranslationConfiguration(ContainerBuilder $container)
    {
        $configFile = __DIR__ . '/../Resources/config/bazinga_js_translation.yaml';
        $config = Yaml::parseFile($configFile);
        $container->prependExtensionConfig('bazinga_js_translation', $config);
        $container->addResource(new FileResource($configFile));
    }

    private function prependQuickReviewSettings(ContainerBuilder $container): void
    {
        $quickReviewFile = __DIR__ . '/../Resources/config/quick_review.yaml';
        $config = Yaml::parseFile($quickReviewFile);
        $container->prependExtensionConfig('ibexa', $config);
        $container->addResource(new FileResource($quickReviewFile));
    }

    private function prependJMSTranslation(ContainerBuilder $container): void
    {
        $container->prependExtensionConfig('jms_translation', [
            'configs' => [
                'ibexa_workflow' => [
                    'dirs' => [
                        __DIR__ . '/../../',
                    ],
                    'output_dir' => __DIR__ . '/../Resources/translations/',
                    'output_format' => 'xliff',
                    'excluded_dirs' => ['Behat', 'Tests', 'node_modules'],
                    'extractors' => [],
                ],
            ],
        ]);
    }

    private function shouldLoadTestServices(ContainerBuilder $container): bool
    {
        return $container->hasParameter('ibexa.behat.browser.enabled')
            && true === $container->getParameter('ibexa.behat.browser.enabled');
    }

    private function hasPageBuilderBundle(ContainerBuilder $container): bool
    {
        $bundles = $container->getParameter('kernel.bundles');

        return isset($bundles['IbexaPageBuilderBundle']);
    }
}

class_alias(IbexaWorkflowExtension::class, 'EzSystems\EzPlatformWorkflowBundle\DependencyInjection\EzPlatformWorkflowExtension');
