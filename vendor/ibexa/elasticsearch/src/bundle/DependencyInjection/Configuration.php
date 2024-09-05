<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Elasticsearch\DependencyInjection;

use Elasticsearch\ConnectionPool\Selectors\RoundRobinSelector;
use Elasticsearch\ConnectionPool\StaticNoPingConnectionPool;
use Ibexa\Elasticsearch\ElasticSearch\Client\Config\Authentication;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $builder = new TreeBuilder(IbexaElasticsearchExtension::EXTENSION_NAME);

        $rootNode = $builder->getRootNode();
        $rootNode
            ->children()
                ->arrayNode('connections')
                    ->defaultValue(['default' => []])
                    ->arrayPrototype()
                        ->children()
                            ->arrayNode('hosts')
                                ->arrayPrototype()
                                    ->beforeNormalization()
                                        ->ifString()
                                        ->then(static function (string $dsn): array {
                                            return ['dsn' => $dsn];
                                        })
                                    ->end()
                                    ->children()
                                        ->scalarNode('dsn')
                                            ->defaultValue(null)
                                        ->end()
                                        ->scalarNode('host')
                                            ->defaultValue('localhost')
                                        ->end()
                                        ->scalarNode('port')
                                            ->defaultValue(9200)
                                        ->end()
                                        ->scalarNode('scheme')
                                            ->defaultValue('http')
                                        ->end()
                                        ->scalarNode('path')
                                            ->defaultValue(null)
                                        ->end()
                                        ->scalarNode('user')
                                            ->defaultValue(null)
                                        ->end()
                                        ->scalarNode('pass')
                                            ->defaultValue(null)
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                            ->scalarNode('elastic_cloud_id')->defaultNull()->end()
                            ->arrayNode('authentication')
                                ->children()
                                    ->enumNode('type')
                                        ->defaultNull()
                                        ->values([Authentication::TYPE_BASIC, Authentication::TYPE_API_KEY])
                                    ->end()
                                    ->variableNode('credentials')
                                        ->info('e.g. ["username", "password"]')
                                        ->defaultValue(['', ''])
                                    ->end()
                                ->end()
                            ->end()
                            ->arrayNode('ssl')
                                ->children()
                                    ->booleanNode('verification')->defaultTrue()->end()
                                    ->arrayNode('ca_cert')
                                        ->children()
                                            ->scalarNode('path')->isRequired()->cannotBeEmpty()->end()
                                        ->end()
                                    ->end()
                                    ->arrayNode('cert')
                                        ->children()
                                            ->scalarNode('path')->isRequired()->cannotBeEmpty()->end()
                                            ->scalarNode('pass')->defaultNull()->end()
                                        ->end()
                                    ->end()
                                    ->arrayNode('cert_key')
                                        ->children()
                                            ->scalarNode('path')->isRequired()->cannotBeEmpty()->end()
                                            ->scalarNode('pass')->defaultNull()->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                            ->scalarNode('connection_pool')
                                ->info('https://www.elastic.co/guide/en/elasticsearch/client/php-api/current/connection_pool.html')
                                ->defaultValue(StaticNoPingConnectionPool::class)
                            ->end()
                            ->scalarNode('connection_selector')
                                ->info('https://www.elastic.co/guide/en/elasticsearch/client/php-api/current/selectors.html')
                                ->defaultValue(RoundRobinSelector::class)
                            ->end()
                            ->integerNode('retries')
                                ->info('https://www.elastic.co/guide/en/elasticsearch/client/php-api/current/configuration.html#_set_retries')
                                ->defaultNull()
                            ->end()
                            ->arrayNode('index_templates')
                                ->performNoDeepMerging()
                                ->defaultValue(['default'])
                                ->info('Index templates used by this connection')
                                ->example(['default', 'custom'])
                                ->scalarPrototype()->end()
                            ->end()
                            ->booleanNode('debug')->defaultFalse()->end()
                            ->booleanNode('trace')->defaultFalse()->end()
                        ->end()
                    ->end()
                ->end()
                ->scalarNode('default_connection')
                    ->info('Default connection name')
                    ->defaultValue('default')
                ->end()
                ->scalarNode('document_group_resolver')
                    ->info('Service ID of strategy used to group documents')
                    ->defaultValue('ibexa.elasticsearch.index.group.default_group_resolver')
                ->end()
                ->arrayNode('index_templates')
                    ->performNoDeepMerging()
                    ->useAttributeAsKey('name')
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('name')
                                ->info('Name of the index template')
                                ->example(['custom'])
                            ->end()
                            ->arrayNode('patterns')
                                ->info('List of wildcards used to match the names of indices')
                                ->example(['custom_*'])
                                ->scalarPrototype()->end()
                            ->end()
                            ->variableNode('settings')
                                ->info('Configuration options for the index. See https://www.elastic.co/guide/en/elasticsearch/reference/current/index-modules.html#index-modules-settings')
                            ->end()
                            ->variableNode('mappings')
                                ->info('Mapping for fields in the index. See https://www.elastic.co/guide/en/elasticsearch/reference/current/mapping.html')
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $builder;
    }
}

class_alias(Configuration::class, 'Ibexa\Platform\Bundle\ElasticSearchEngine\DependencyInjection\Configuration');
