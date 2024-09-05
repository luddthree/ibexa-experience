<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Taxonomy\DependencyInjection;

use Ibexa\Taxonomy\Service\TaxonomyConfiguration;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('ibexa_taxonomy');

        $treeBuilder
            ->getRootNode()
                ->children()
                    ->arrayNode('taxonomies')
                        ->useAttributeAsKey('identifier')
                        ->arrayPrototype()
                            ->children()
                                ->scalarNode(TaxonomyConfiguration::CONFIG_PARENT_LOCATION_REMOTE_ID)
                                    ->info('Parent Location remote ID where this taxonomy is stored')
                                    ->isRequired()
                                ->end()
                                ->scalarNode(TaxonomyConfiguration::CONFIG_CONTENT_TYPE)
                                    ->info('Unique content type used for this taxonomy')
                                ->end()
                                ->booleanNode(TaxonomyConfiguration::CONFIG_REGISTER_MAIN_MENU)
                                    ->info('Add taxonomy to main menu')
                                    ->defaultTrue()
                                ->end()
                                ->arrayNode(TaxonomyConfiguration::CONFIG_FIELD_MAPPING)
                                    ->info('Field identifiers to map data from content to taxonomy')
                                    ->children()
                                        ->scalarNode('identifier')
                                            ->isRequired()
                                            ->info('Identifier field, only supports ibexa_string field type')
                                        ->end()
                                        ->scalarNode('parent')
                                            ->isRequired()
                                            ->info('Parent field, only supports ibexa_taxonomy_entry field type')
                                        ->end()
                                        ->scalarNode('name')
                                            ->info('Name field, used to autogenerate identifier from')
                                        ->end()
                                    ->end()
                                ->end()
                                ->booleanNode(TaxonomyConfiguration::CONFIG_ASSIGNED_CONTENT_TAB)
                                    ->info('Add assigned content tab to a taxonomy')
                                    ->defaultTrue()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
