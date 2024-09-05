<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\FormBuilder\DependencyInjection;

use Ibexa\FormBuilder\Definition\FieldDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * @deprecated since Ibexa 4.0, use
     * {@see \Ibexa\Bundle\FormBuilder\DependencyInjection\IbexaFormBuilderExtension::EXTENSION_NAME}
     * instead.
     */
    public const TREE_ROOT = IbexaFormBuilderExtension::EXTENSION_NAME;

    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder(IbexaFormBuilderExtension::EXTENSION_NAME);

        $rootNode = $treeBuilder->getRootNode();
        $rootNode
            ->children()
                ->arrayNode('fields')
                    ->useAttributeAsKey('identifier')
                    ->prototype('array')
                    ->children()
                        ->scalarNode('name')->end()
                        ->scalarNode('category')
                            ->defaultValue(FieldDefinition::DEFAULT_CATEGORY)
                        ->end()
                        ->scalarNode('thumbnail')
                            ->defaultValue(null)
                        ->end()
                        ->arrayNode('validators')
                            ->defaultValue([])
                            ->useAttributeAsKey('identifier')
                            ->prototype('array')
                                ->children()
                                    ->variableNode('default_value')
                                        ->defaultValue(null)
                                    ->end()
                                    ->scalarNode('name')
                                        ->defaultValue(null)
                                    ->end()
                                    ->scalarNode('category')
                                        ->defaultValue('default')
                                    ->end()
                                    ->variableNode('options')
                                        ->defaultValue([])
                                    ->end()
                                    ->arrayNode('validators')
                                        ->info('Constraints of the attribute value')
                                        ->useAttributeAsKey('identifier')
                                        ->prototype('array')
                                            ->children()
                                                ->scalarNode('message')->end()
                                                ->variableNode('options')->end()
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('attributes')
                            ->defaultValue([])
                            ->useAttributeAsKey('identifier')
                            ->prototype('array')
                            ->children()
                                ->variableNode('default_value')
                                    ->defaultValue(null)
                                ->end()
                                ->scalarNode('name')->end()
                                ->scalarNode('category')
                                    ->defaultValue('default')
                                ->end()
                                ->scalarNode('type')->end()
                                ->variableNode('options')
                                    ->defaultValue([])
                                    ->info('Options are used by types select, multiple, radio')
                                ->end()
                                ->arrayNode('validators')
                                    ->info('Constraints of the attribute value')
                                    ->useAttributeAsKey('identifier')
                                    ->prototype('array')
                                        ->children()
                                            ->scalarNode('message')->end()
                                            ->variableNode('options')->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}

class_alias(Configuration::class, 'EzSystems\EzPlatformFormBuilderBundle\DependencyInjection\Configuration');
