<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Taxonomy\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;
use Symfony\Component\Yaml\Yaml;

final class IbexaTaxonomyExtension extends ConfigurableExtension implements PrependExtensionInterface
{
    /**
     * @param array<mixed> $mergedConfig
     */
    public function loadInternal(array $mergedConfig, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );

        $loader->load('services.yaml');

        if ($mergedConfig['taxonomies']) {
            $container->setParameter('ibexa.taxonomy.taxonomies', $mergedConfig['taxonomies']);
        }

        $shouldLoadTestServices = $this->shouldLoadTestServices($container);
        if ($shouldLoadTestServices) {
            $loader->load('services/test/feature_contexts.yaml');
            $loader->load('services/test/pages.yaml');
            $loader->load('services/test/components.yaml');
            $loader->load('services/test/data_providers.yaml');
        }
    }

    public function prepend(ContainerBuilder $container): void
    {
        $config = Yaml::parse(file_get_contents(__DIR__ . '/../Resources/config/ibexa.yaml'));

        $container->prependExtensionConfig('ibexa', $config);

        $config = Yaml::parse(file_get_contents(__DIR__ . '/../Resources/config/stof_doctrine_extensions.yaml'));

        $container->prependExtensionConfig('stof_doctrine_extensions', $config);

        $this->prependJMSTranslation($container);
        $this->prependBazingaJsTranslationConfiguration($container);
        $this->prependStofDoctrineExtensionsConfiguration($container);
        $this->prependGraphQL($container);
    }

    public function prependJMSTranslation(ContainerBuilder $container): void
    {
        $container->prependExtensionConfig('jms_translation', [
            'configs' => [
                'ibexa_taxonomy' => [
                    'dirs' => [
                        __DIR__ . '/../../',
                    ],
                    'output_dir' => __DIR__ . '/../Resources/translations/',
                    'output_format' => 'xliff',
                    'excluded_dirs' => ['Behat', 'CDP', 'Tests', 'node_modules'],
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

    private function prependStofDoctrineExtensionsConfiguration(ContainerBuilder $container): void
    {
        $ibexaConfig = $container->getExtensionConfig('ibexa');
        $connections = [];

        // retrieve all database connections used by Ibexa
        foreach ($ibexaConfig as $config) {
            if (!isset($config['repositories'])) {
                continue;
            }

            foreach ($config['repositories'] as $repositoryConfig) {
                if (!isset($repositoryConfig['storage']['connection'])) {
                    continue;
                }

                $connections[] = $repositoryConfig['storage']['connection'];
            }
        }

        $connections = array_unique($connections);

        if (empty($connections)) {
            return;
        }

        // enable tree doctrine extension for every connection
        $extensionConfig = [
            'orm' => [],
        ];

        foreach ($connections as $connection) {
            $extensionConfig['orm'][$connection] = [
                'tree' => true,
            ];
        }

        // inject the config to be parsed by Stof Doctrine Extensions bundle
        $container->prependExtensionConfig('stof_doctrine_extensions', $extensionConfig);
    }

    private function prependGraphQL(ContainerBuilder $container): void
    {
        $container->prependExtensionConfig('overblog_graphql', [
            'definitions' => [
                'mappings' => [
                    'types' => [
                        [
                            'type' => 'yaml',
                            'dir' => __DIR__ . '/../Resources/config/graphql/types',
                        ],
                    ],
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
