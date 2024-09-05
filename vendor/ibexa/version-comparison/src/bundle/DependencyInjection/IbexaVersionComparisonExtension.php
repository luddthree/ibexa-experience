<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\VersionComparison\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Yaml\Yaml;

class IbexaVersionComparisonExtension extends Extension implements PrependExtensionInterface
{
    public const EXTENSION_NAME = 'ibexa_version_comparison';

    public function getAlias(): string
    {
        return self::EXTENSION_NAME;
    }

    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $this->setHtmlComparisonDefaultValueParameters($container, $config['html']);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yaml');

        if ($this->shouldLoadTestServices($container)) {
            $loader->load('test/feature_contexts.yaml');
            $loader->load('test/pages.yaml');
            $loader->load('test/components.yaml');
        }
    }

    public function prepend(ContainerBuilder $container)
    {
        $config = Yaml::parse(file_get_contents(__DIR__ . '/../Resources/config/ez_comparison_result_templates.yaml'));
        $container->prependExtensionConfig('ibexa', $config);

        $this->prependJMSTranslation($container);
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

    private function setHtmlComparisonDefaultValueParameters(
        ContainerBuilder $container,
        array $htmlComparisonConfig
    ): void {
        foreach ($htmlComparisonConfig as $key => $value) {
            $container->setParameter('ezplatform.version_comparison.html.' . $key, $value);
        }
    }

    private function shouldLoadTestServices(ContainerBuilder $container): bool
    {
        return $container->hasParameter('ibexa.behat.browser.enabled')
            && true === $container->getParameter('ibexa.behat.browser.enabled');
    }
}

class_alias(IbexaVersionComparisonExtension::class, 'EzSystems\EzPlatformVersionComparisonBundle\DependencyInjection\EzPlatformVersionComparisonExtension');
