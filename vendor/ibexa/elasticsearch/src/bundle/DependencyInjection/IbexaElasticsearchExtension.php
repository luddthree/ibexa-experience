<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Elasticsearch\DependencyInjection;

use Ibexa\Contracts\Elasticsearch\ElasticSearch\Index\Group\GroupResolverInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Yaml\Yaml;

final class IbexaElasticsearchExtension extends Extension implements PrependExtensionInterface
{
    public const EXTENSION_NAME = 'ibexa_elasticsearch';

    public function load(array $configs, ContainerBuilder $container): void
    {
        $config = $this->processConfiguration(new Configuration(), $configs);

        $container->setParameter(
            'ibexa.elasticsearch.client_configuration_provider.available_configurations',
            $config['connections'] ?? []
        );

        $container->setParameter(
            'ibexa.elasticsearch.client_configuration_provider.default_configuration_name',
            $config['default_connection'] ?? null
        );

        $container->setParameter(
            'ibexa.elasticsearch.index_templates',
            $config['index_templates']
        );

        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );

        $loader->load('services-sa.yaml');

        $container->setAlias(
            GroupResolverInterface::class,
            $config['document_group_resolver']
        );
    }

    public function getAlias(): string
    {
        return self::EXTENSION_NAME;
    }

    public function prepend(ContainerBuilder $container): void
    {
        $container->prependExtensionConfig(
            $this->getAlias(),
            Yaml::parseFile(
                __DIR__ . '/../Resources/config/default-config.yaml'
            )
        );
    }
}

class_alias(IbexaElasticsearchExtension::class, 'Ibexa\Platform\Bundle\ElasticSearchEngine\DependencyInjection\PlatformElasticSearchEngineExtension');
